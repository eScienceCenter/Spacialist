import { useAuth } from '../bootstrap/auth.js';
import store from '../bootstrap/store.js';

const auth = useAuth();

export function can(permissionString, oneOf) {
    oneOf = oneOf || false;
    const user = store.getters.user;
    if(!user) return false;
    const permissions = permissionString.split('|');
    const hasPermission = permission => {
        return user.permissions[permission] === 1;
    };

    if(oneOf) {
        return permissions.some(hasPermission);
    } else {
        return permissions.every(hasPermission);
    }
}

export function getErrorMessages(error, suffix = '') {
    let msgObject = {};
    const r = error.response;
    if(r.status == 422) {
        if(r.data.errors) {
            for(let k in r.data.errors) {
                msgObject[`${k}${suffix}`] = r.data.errors[k];
            }
        }
    } else if(r.status == 400) {
        msgObject.global = r.data.error;
    }
    return msgObject;
}

export function rgb2hex(rgb) {
    let colors = rgb.substring(1);
    let r = parseInt(colors.substring(0, 2), 16);
    let g = parseInt(colors.substring(2, 4), 16);
    let b = parseInt(colors.substring(4, 6), 16);
    return [r, g, b];
}

export function getTs() {
    const d = new Date();
    return d.getTime();
}

export function hasConcept(url) {
    if(!url) return false;
    return !!store.getters.concepts[url];
}

export function translateLabel(element, prop) {
    const value = element[prop];
    if(!value) return element;
    return translateConcept(value);
}

export function translateConcept(url) {
    const concepts = store.getters.concepts;
    if(!url || !concepts) return url;
    if(!concepts[url]) return url;
    return concepts[url].label;
}

export function translateEntityType(id) {
    return translateConcept(getEntityType(id).thesaurus_url);
}

export function getEntityType(id) {
    return getEntityTypes()[id];
}

export function getEntityTypes() {
    return store.getters.entityTypes;
}

// Formula based on https://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color/3943023#3943023
export function getEntityColors(id, alpha = 0.5) {
    const et = getEntityType(id);
    if(!et || !et.layer) return {};
    let r, g, b, a;
    [r, g, b] = rgb2hex(et.layer.color);
    const cs = [r, g, b].map(c => {
        c /= 255.0;
        if(c <= 0.03928) c /= 12.92;
        else c = Math.pow(((c+0.055)/1.055), 2.4);
        return c;
    });
    // let cont = r*0.299 + g*0.587 + b*0.114;
    const l = cs[0]*0.2126 + cs[1]*0.7152 + cs[2]*0.0722;

    // const textColor = cont > 150 ? '#000000' : '#ffffff';
    const textColor = l > 0.179 ? '#000000' : '#ffffff';
    const color = `rgba(${r}, ${g}, ${b}, ${alpha})`;
    return {
        color: textColor,
        backgroundColor: color
    };
}

// UNVERIFIED

export function simpleResourceType(resource) {
    switch(resource) {
        case 'App\\Entity':
            return 'entity';
        case 'App\\Attribute':
            return 'attribute';
        case 'App\\AttributeValue':
        case 'attribute_values':
            return 'attribute_value';
        default:
            return resource;
    }
};

export function getNotificationSourceLink(notification) {
    const query = this.$route.query;
    switch(simpleResourceType(notification.data.resource.type)) {
        case 'entity':
            return {
                name: 'entitydetail',
                params: {
                    id: notification.data.resource.id
                },
                query: {
                    ...query,
                    view: 'comments',
                }
            }
        case 'attribute_value':
            return {
                name: 'entityrefs',
                params: {
                    id: notification.data.resource.meta.entity_id,
                    aid: notification.data.resource.meta.attribute_id,
                },
                query: query
            }
        default:
            return null;
    }
};

export function postComment(content, resource, replyToId = null, metadata = null, onFinish = null) {
    let data = {
        content: content,
        resource_id: resource.id,
        resource_type: simpleResourceType(resource.type),
    };
    if(replyToId) {
        data.reply_to = replyToId;
    }
    if(metadata) {
        data.metadata = metadata;
    }
    $httpQueue.add(() => axios.post(`/comment`, data).then(response => {
        if(onFinish) {
            onFinish(response.data);
        }
    }));
};

export function userNotifications() {
    return getUser().notifications || [];
};

export function markAsRead(id, from = $userNotifications()) {
    const elem = from.find(elem => elem.id === id)
    if(elem) {
        return $httpQueue.add(() => axios.patch(`notification/read/${id}`).then(response => {
            elem.read_at = Date();
        }));
    }
}

export function markAllAsRead(ids = null, from = userNotifications()) {
    if(!ids) {
        ids = from.map(elem => elem.id);
    }
    const data = {
        ids: ids
    };
    return $httpQueue.add(() => axios.patch(`notification/read/`, data).then(response => {
        let idsC = _clone(ids);
        from.forEach(elem => {
            const idx = idsC.findIndex(id => id === elem.id);
            if(idx > -1) {
                elem.read_at = Date();
                idsC.splice(idx, 1);
            }
        });
    }));
}

export function deleteNotification(id, from = userNotifications()) {
    return $httpQueue.add(() => axios.delete(`notification/${id}`).then(response => {
        const idx = from.findIndex(elem => elem.id === id);
        if(idx > -1) {
            from.splice(idx, 1);
        }
    }));
}

export function deleteAllNotifications(ids = null, from = userNotifications()) {
    if(!ids) {
        ids = from.map(elem => elem.id);
    }
    const data = {
        ids: ids
    };
    return $httpQueue.add(() => axios.patch(`notification/`, data).then(response => {
        ids.forEach(id => {
            const idx = from.findIndex(elem => elem.id === id);
            if(idx > -1) {
                from.splice(idx, 1);
            }
        });
    }));
}

export function getUser() {
    return isLoggedIn() ? auth.user() : {};
}

export function userId() {
    return getUser().id || -1;
};

export function getUserBy(value, attr = 'id') {
    if(isLoggedIn()) {
        const isNum = !isNaN(value);
        const lValue = isNum ? value : value.toLowerCase();
        return this.state.users.find(u => isNum ? (u[attr] == lValue) : (u[attr].toLowerCase() == lValue));
    } else {
        return null;
    }
};

export function getUsers() {
    return isLoggedIn() ? this.state.users : [];
};

export async function asyncFor(arr, callback) {
    for(let i=0; i<arr.length; i++) {
        await callback(arr[i]);
    }
};

export function showToast(title, text, type, duration) {
    type = type || 'info'; // success, info, warn, error
    duration = duration || 2000;
    this.$notify({
        group: 'spacialist',
        title: title,
        text: text,
        type: type,
        duration: duration
    });
};

export function throwError(error) {
    if(error.response) {
        const r = error.response;
        const req = {
            status: r.status,
            url: r.config.url,
            method: r.config.method.toUpperCase()
        };
        this.$showErrorModal(r.data, r.headers, req);
    } else if(error.request) {
        this.$showErrorModal(error.request);
    } else {
        this.$showErrorModal(error.message || error);
    }
};

export function getValidClass(msgObject, field) {
    let isInvalid = false;
    field.split('|').forEach(f => {
        if(!!msgObject[f]) {
            isInvalid = true;
        }
    });

    return {
        // 'is-valid': !msgObject[field],
        'is-invalid': isInvalid
    };
}

export function showErrorModal(errorMsg, headers, request) {
    this.$modal.show('error-modal', {msg: errorMsg, headers: headers, request: request});
};

export function createDownloadLink(content, filename, base64 = false, contentType = 'text/plain') {
    var link = document.createElement("a");
    let url;
    if(base64) {
        url = `data:${contentType};base64,${content}`;
    } else {
        url = window.URL.createObjectURL(new Blob([content]));
    }
    link.setAttribute("href", url);
    link.setAttribute("type", contentType);
    link.setAttribute("download", filename);
    document.body.appendChild(link);
    link.click();
}

export function showUserInfo(user) {
    console.log("Implement showUserInfo method");
    // TODO
    // this.$modal.show('user-info-modal', {
    //     user: user,
    // });
}

export function hasPreference(prefKey, prop) {
    const ps = store.getters.preferenceByKey(prefKey);
    if(ps) {
        return ps[prop] || ps;
    }
}

export function getPreference(prefKey) {
    return store.getters.preferenceByKey(prefKey);
}

export function setPreference(prefKey, value) {
    this.state.preferences[prefKey] = value;
}

export function getTabPlugins() {
    return this.state.plugins.tab;
}

export function getToolPlugins() {
    return this.state.plugins.tools;
}

export function getSettingsPlugins() {
    return this.state.plugins.settings;
}

export function getPlugins() {
    return this.state.plugins;
}

export function addEntityType(entityType) {
    this.state.entityTypes[entityType.id] = entityType;
}
