// Validators
Vue.prototype.$validateObject = function(value) {
    // concepts is valid if it is either an object
    // or an empty array
    // (empty assoc arrays are simple arrays in php)
    return typeof value == 'object' || (typeof value == 'array' && value.length == 0);
};

// Directives
Vue.directive('can', {
    terminal: true,
    bind: function(el, bindings) {
        const canI = Vue.prototype.$can(bindings.value, bindings.modifiers.one);

        if(!canI) {
            this.warning = document.createElement('p');
            this.warning.className = 'alert alert-warning v-can-warning';
            this.warning.innerHTML = 'You do not have permission to access this page';
            for(let i=0; i<el.children.length; i++) {
                let c = el.children[i];
                c.classList.add('v-can-hidden');
            }
            el.appendChild(this.warning);
        }
    },
    unbind: function(el) {
        if(!el.children) return;
        for(let i=0; i<el.children.length; i++) {
            let c = el.children[i];
            // remove our warning elem
            if(c.classList.contains('v-can-warning')) {
                el.removeChild(c);
                continue;
            }
            if(c.classList.contains('v-can-hidden')) {
                c.classList.remove('v-can-hidden');
            }
        }
    }
});

// Prototype
Vue.prototype.$can = function(permissionString, oneOf) {
    oneOf = oneOf || false;
    const user = this.$auth.user();
    if(!user) return false;
    const permissions = permissionString.split('|');
    const hasPermission = function(permission) {
        return user.permissions[permission] === 1;
    };

    if(oneOf) {
        return permissions.some(hasPermission);
    } else {
        return permissions.every(hasPermission);
    }
}

Vue.prototype.$isLoggedIn = function() {
    return this.$auth.check();
};

Vue.prototype.$simpleResourceType = function(resource) {
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

Vue.prototype.$getNotificationSourceLink = function(notification) {
    const query = this.$route.query;
    switch(this.$simpleResourceType(notification.data.resource.type)) {
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

Vue.prototype.$postComment = function(content, resource, replyToId = null, metadata = null, onFinish = null) {
    let data = {
        content: content,
        resource_id: resource.id,
        resource_type: this.$simpleResourceType(resource.type),
    };
    if(replyToId) {
        data.reply_to = replyToId;
    }
    if(metadata) {
        data.metadata = metadata;
    }
    $httpQueue.add(() => $http.post(`/comment`, data).then(response => {
        if(onFinish) {
            onFinish(response.data);
        }
    }));
};

Vue.prototype.$userNotifications = function() {
    return this.$isLoggedIn() ? this.$auth.user().notifications : [];
};

Vue.prototype.$markAsRead = function(id, from = this.$userNotifications()) {
    const elem = from.find(elem => elem.id === id)
    if(elem) {
        return $httpQueue.add(() => $http.patch(`notification/read/${id}`).then(response => {
            elem.read_at = Date();
        }));
    }
}

Vue.prototype.$markAllAsRead = function(ids = null, from = this.$userNotifications()) {
    if(!ids) {
        ids = from.map(elem => elem.id);
    }
    const data = {
        ids: ids
    };
    return $httpQueue.add(() => $http.patch(`notification/read/`, data).then(response => {
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

Vue.prototype.$deleteNotification = function(id, from = this.$userNotifications()) {
    return $httpQueue.add(() => $http.delete(`notification/${id}`).then(response => {
        const idx = from.findIndex(elem => elem.id === id);
        if(idx > -1) {
            from.splice(idx, 1);
        }
    }));
}

Vue.prototype.$deleteAllNotifications = function(ids = null, from = this.$userNotifications()) {
    if(!ids) {
        ids = from.map(elem => elem.id);
    }
    const data = {
        ids: ids
    };
    return $httpQueue.add(() => $http.patch(`notification/`, data).then(response => {
        ids.forEach(id => {
            const idx = from.findIndex(elem => elem.id === id);
            if(idx > -1) {
                from.splice(idx, 1);
            }
        });
    }));
}

Vue.prototype.$updateLanguage = function() {
    if(this.$isLoggedIn()) {
        Vue.i18n.locale = this.$getPreference('prefs.gui-language');
    }
};

Vue.prototype.$getUser = function() {
    if(this.$isLoggedIn()) {
        return this.$auth.user();
    } else {
        return {};
    }
}

Vue.prototype.$userId = function() {
    if(this.$isLoggedIn()) {
        return this.$auth.user().id;
    } else {
        return -1;
    }
};

Vue.prototype.$getUserBy = function(value, attr = 'id') {
    if(this.$isLoggedIn()) {
        const isNum = !isNaN(value);
        const lValue = isNum ? value : value.toLowerCase();
        return this.$root.$data.users.find(u => isNum ? (u[attr] == lValue) : (u[attr].toLowerCase() == lValue));
    } else {
        return null;
    }
};

Vue.prototype.$getUsers = function() {
    if(Vue.prototype.$auth.check()) {
        return this.$root.$data.users;
    } else {
        return [];
    }
};

Vue.prototype.$asyncFor = async function(arr, callback) {
    for(let i=0; i<arr.length; i++) {
        await callback(arr[i]);
    }
};

Vue.prototype.$showToast = function(title, text, type, duration) {
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

Vue.prototype.$throwError = function(error) {
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

Vue.prototype.$getErrorMessages = function(error, msgObject, suffix = '') {
    for(let k in msgObject) {
        delete msgObject[k];
    }
    const r = error.response;
    if(r.status == 422) {
        if(r.data.errors) {
            for(let k in r.data.errors) {
                Vue.set(msgObject, `${k}${suffix}`, r.data.errors[k]);
            }
        }
    } else if(r.status == 400) {
        Vue.set(msgObject, 'global', r.data.error);
    }
}

Vue.prototype.$getValidClass = function(msgObject, field) {
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

Vue.prototype.$showErrorModal = function(errorMsg, headers, request) {
    this.$modal.show('error-modal', {msg: errorMsg, headers: headers, request: request});
};

Vue.prototype.$createDownloadLink = function(content, filename, base64 = false, contentType = 'text/plain') {
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

Vue.prototype.$rgb2hex = function(rgb) {
    let colors = rgb.substring(1);
    let r = parseInt(colors.substring(0, 2), 16);
    let g = parseInt(colors.substring(2, 4), 16);
    let b = parseInt(colors.substring(4, 6), 16);
    return [r, g, b];
}

Vue.prototype.$getTs = function() {
    const d = new Date();
    return d.getTime();
}

Vue.prototype.$hasConcept = function(url) {
    if(!url) return false;
    return !!this.$root.$data.concepts[url];
}

Vue.prototype.$translateLabel = function(element, prop) {
    const value = element[prop];
    if(!value) return element;
    return this.$translateConcept(value);
}

Vue.prototype.$translateConcept = function(url) {
    const concepts = this.$root.$data.concepts;
    if(!url || !concepts) return url;
    if(!concepts[url]) return url;
    return concepts[url].label;
}

Vue.prototype.$translateEntityType = function(id) {
    return this.$translateConcept(this.$root.$data.entityTypes[id].thesaurus_url);
}

Vue.prototype.$getEntityType = function(id) {
    return this.$root.$data.entityTypes[id];
}

Vue.prototype.$getEntityTypes = function() {
    return this.$root.$data.entityTypes;
}

// Formula based on https://stackoverflow.com/questions/3942878/how-to-decide-font-color-in-white-or-black-depending-on-background-color/3943023#3943023
Vue.prototype.$getEntityColors = function(id, alpha = 0.5) {
    const et = this.$getEntityType(id);
    if(!et || !et.layer) return {};
    let r, g, b, a;
    [r, g, b] = this.$rgb2hex(et.layer.color);
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

Vue.prototype.$showUserInfo = function(user) {
    this.$modal.show('user-info-modal', {
        user: user,
    });
}

Vue.prototype.$hasPreference = function(prefKey, prop) {
    const ps = this.$root.$data.preferences;
    return prop ? ps[prefKey] && ps[prefKey][prop] : ps[prefKey];
}

Vue.prototype.$getPreference = function(prefKey) {
    return this.$root.$data.preferences[prefKey];
}

Vue.prototype.$setPreference = function(prefKey, value) {
    this.$root.$data.preferences[prefKey] = value;
}

Vue.prototype.$getTabPlugins = function() {
    return this.$root.$data.plugins.tab;
}

Vue.prototype.$getToolPlugins = function() {
    return this.$root.$data.plugins.tools;
}

Vue.prototype.$getSettingsPlugins = function() {
    return this.$root.$data.plugins.settings;
}

Vue.prototype.$getPlugins = function() {
    return this.$root.$data.plugins;
}

Vue.prototype.$addEntityType = function(entityType) {
    this.$root.$data.entityTypes[entityType.id] = entityType;
}
