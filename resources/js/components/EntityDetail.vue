<template>
    <div class="h-100 d-flex flex-column">
        <entity-breadcrumbs class="mb-2 small" :entity="selectedEntity" v-if="selectedEntity.parentIds.length > 1"></entity-breadcrumbs>
        <div class="d-flex align-items-center justify-content-between">
            <h3 class="mb-0" @mouseenter="onEntityHeaderHover(true)" @mouseleave="onEntityHeaderHover(false)">
                <span v-if="!selectedEntity.editing">
                    {{ selectedEntity.name }}
                    <small>
                        <span v-show="hiddenAttributes > 0" @mouseenter="dependencyInfoHoverOver" @mouseleave="dependencyInfoHoverOut">
                            <i id="dependency-info" class="fas fa-fw fa-xs fa-eye-slash"></i>
                        </span>
                    </small>
                    <a href="#" v-if="entityHeaderHovered" class="text-dark" @click.prevent="enableEntityNameEditing()">
                        <i class="fas fa-fw fa-edit fa-xs"></i>
                    </a>
                </span>
                <form class="form-inline" v-else>
                    <input type="text" class="form-control mr-2" v-model="newEntityName" />
                    <button type="submit" class="btn btn-outline-success mr-2" @click="updateEntityName(selectedEntity, newEntityName)">
                        <i class="fas fa-fw fa-check"></i>
                    </button>
                    <button type="reset" class="btn btn-outline-danger" @click="cancelUpdateEntityName()">
                        <i class="fas fa-fw fa-ban"></i>
                    </button>
                </form>
            </h3>
            <span>
                <button type="submit" form="entity-attribute-form" class="btn btn-success" :disabled="!isFormDirty || !$can('duplicate_edit_concepts')">
                    <i class="fas fa-fw fa-save"></i> {{ $t('global.save') }}
                </button>
                <button type="button" class="btn btn-danger" :disabled="!$can('delete_move_concepts')" @click="deleteEntity(selectedEntity)">
                    <i class="fas fa-fw fa-trash"></i> {{ $t('global.delete') }}
                </button>
            </span>
        </div>
        <div class="d-flex justify-content-between my-2">
            <div>
                <span :style="colorStyles">
                    <i class="fas fa-fw fa-circle"></i>
                </span>
                {{
                    $translateConcept($getEntityType(selectedEntity.entity_type_id).thesaurus_url)
                }}
            </div>
            <div>
                <span>
                    {{ (selectedEntity.updated_at || selectedEntity.created_at) | date(undefined, true, true) }}
                </span>
                -
                <i class="fas fa-fw fa-user-edit"></i>
                <span class="font-weight-medium">
                    {{ selectedEntity.lasteditor }}
                </span>
            </div>
        </div>
        <form id="entity-attribute-form" name="entity-attribute-form" class="col pl-0 pr-0 overflow-hidden" @submit.prevent="saveEntity(selectedEntity)">
            <attributes class="pt-2 h-100 scroll-y-auto scroll-x-hidden" v-if="hasData" v-can="'view_concept_props'"
                :attributes="selectedEntity.attributes"
                :dependencies="selectedEntity.dependencies"
                :disable-drag="true"
                :on-metadata="showMetadata"
                :metadata-addon="hasReferenceGroup"
                :selections="selectedEntity.selections"
                :values="selectedEntity.data"
                @attr-dep-change="updateDependencyCounter"
                @handle-moderation="handleModeration">
            </attributes>
        </form>

        <router-view
            v-can="'view_concept_props'"
            :bibliography="bibliography"
            :refs="attributeReferences">
        </router-view>
    </div>
</template>

<script>
    import { EventBus } from '../event-bus.js';

    export default {
        beforeRouteEnter(to, from, next) {
            next(vm => vm.getEntityData(vm.selectedEntity));
        },
        beforeRouteUpdate(to, from, next) {
            if(to.params.id != from.params.id) {
                this.getEntityData(this.selectedEntity).then(r => {
                    next();
                });
            } else {
                if(to.params.aid) {
                    this.setReferenceAttribute(to.params.aid);
                }
                next();
            }
        },
        props: {
            selectedEntity: {
                required: true,
                type: Object
            },
            bibliography: {
                required: false,
                type: Array,
                default: () => []
            },
            onDelete: {
                required: false,
                type: Function,
                default: () => {}
            }
        },
        mounted() {},
        methods: {
            init(entity) {},
            getEntityData(entity) {
                this.dataLoaded = false;
                if(!this.$can('view_concept_props')) {
                    Vue.set(this.selectedEntity, 'data', {});
                    Vue.set(this.selectedEntity, 'attributes', []);
                    Vue.set(this.selectedEntity, 'selections', {});
                    Vue.set(this.selectedEntity, 'dependencies', []);
                    Vue.set(this.selectedEntity, 'references', []);
                    Vue.set(this, 'dataLoaded', true);
                    return new Promise(r => r(null));
                }
                const cid = entity.id;
                const ctid = entity.entity_type_id;
                return $httpQueue.add(() => $http.get(`/entity/${cid}/data`).then(response => {
                    // if result is empty, php returns [] instead of {}
                    if(response.data instanceof Array) {
                        response.data = {};
                    }
                    Vue.set(this.selectedEntity, 'data', response.data);
                    return $http.get(`/editor/entity_type/${ctid}/attribute`);
                }).then(response => {
                    this.selectedEntity.attributes = [];
                    let data = response.data;
                    for(let i=0; i<data.attributes.length; i++) {
                        let aid = data.attributes[i].id;
                        if(!this.selectedEntity.data[aid]) {
                            let val = {};
                            switch(data.attributes[i].datatype) {
                                case 'dimension':
                                case 'epoch':
                                case 'timeperiod':
                                    val.value = {};
                                    break;
                                case 'table':
                                case 'list':
                                    val.value = [];
                                    break;
                            }
                            Vue.set(this.selectedEntity.data, aid, val);
                        } else {
                            const val = this.selectedEntity.data[aid].value;
                            switch(data.attributes[i].datatype) {
                                case 'date':
                                    const dtVal = new Date(val);
                                    this.selectedEntity.data[aid].value = dtVal;
                                    break;
                            }
                        }
                        this.selectedEntity.attributes.push(data.attributes[i]);
                    }
                    // if result is empty, php returns [] instead of {}
                    if(data.selections instanceof Array) {
                        data.selections = {};
                    }
                    if(data.dependencies instanceof Array) {
                        data.dependencies = {};
                    }
                    Vue.set(this.selectedEntity, 'selections', data.selections);
                    Vue.set(this.selectedEntity, 'dependencies', data.dependencies);

                    const aid = this.$route.params.aid;
                    this.setReferenceAttribute(aid);
                    Vue.set(this, 'dataLoaded', true);
                }));
            },
            saveEntity(entity) {
                if(!this.$can('duplicate_edit_concepts')) return;
                let cid = entity.id;
                let pendings = [];
                var patches = [];
                for(let f in this.fields) {
                    if(this.fields.hasOwnProperty(f) && f.startsWith('attribute-')) {
                        if(this.fields[f].dirty) {
                            let aid = Number(f.replace(/^attribute-/, ''));
                            let data = entity.data[aid];
                            var patch = {};
                            patch.params = {};
                            patch.params.aid = aid;
                            patch.params.cid = cid;
                            if(data.id) {
                                // if data.id exists, there has been an entry in the database, therefore it is a replace/remove operation
                                patch.params.id = data.id;
                                if(data.value && data.value != '') {
                                    // value is set, therefore it is a replace
                                    patch.op = "replace";
                                    patch.value = data.value;
                                    patch.value = this.getCleanValue(data, entity.attributes);
                                    pendings.push({
                                        op: 'replace',
                                        id: aid
                                    });
                                } else {
                                    // value is empty, therefore it is a remove
                                    patch.op = "remove";
                                    pendings.push({
                                        op: 'remove',
                                        id: aid
                                    });
                                }
                            } else {
                                // there has been no entry in the database before, therefore it is an add operation
                                if(data.value && data.value != '') {
                                    patch.op = "add";
                                    data.attribute = entity.attributes.find(a => a.id == aid);
                                    patch.value = this.getCleanValue(data, entity.attributes);
                                    pendings.push({
                                        op: 'add',
                                        id: aid
                                    });
                                } else {
                                    // there has been no entry in the database before and values are not different (should not happen ;))
                                    continue;
                                }
                            }
                            patches.push(patch);
                        }
                    }
                }
                return $httpQueue.add(() => $http.patch('/entity/'+cid+'/attributes', patches).then(response => {
                    this.resetFlags();
                    this.$showToast(
                        this.$t('main.entity.toasts.updated.title'),
                        this.$t('main.entity.toasts.updated.msg', {
                            name: entity.name
                        }),
                        'success'
                    );
                    this.setModificationFields(response.data);
                    // Lock all modified attributes if user needs to be moderated
                    if(this.$moderated()) {
                        for(let i=0; i<pendings.length; i++) {
                            let p = pendings[i];
                            entity.data[p.id].moderation_state = 'pending';
                        }
                    }
                }).catch(error => {
                    const r = error.response;
                    this.$showToast(
                        `${r.status}: ${r.statusText}`,
                        r.data.error,
                        'error',
                        5000
                    );
                })
            );
            },
            deleteEntity(entity) {
                EventBus.$emit('entity-delete', {
                    entity: entity
                });
            },
            onEntityHeaderHover(hoverState) {
                this.entityHeaderHovered = hoverState;
            },
            enableEntityNameEditing() {
                this.newEntityName = this.selectedEntity.name;
                Vue.set(this.selectedEntity, 'editing', true);
            },
            updateEntityName(entity, name) {
                // If name does not change, just cancel
                if(entity.name == name) {
                    this.cancelUpdateEntityName();
                } else {
                    const data = {
                        name: name
                    };
                    $httpQueue.add(() => $http.patch(`entity/${entity.id}/name`, data).then(response => {
                        EventBus.$emit('entity-update', {
                            type: 'name',
                            entity_id: entity.id,
                            value: name
                        });
                        entity.name = name;
                        this.cancelUpdateEntityName();
                    }));
                }
            },
            cancelUpdateEntityName() {
                Vue.set(this.selectedEntity, 'editing', false);
                this.newEntityName = '';
            },
            getCleanValue(entry, attributes) {
                if(!entry) return;
                const v = entry.value;
                switch(entry.attribute.datatype) {
                    case 'string-sc':
                        return {
                            id: v.id,
                            concept_url: v.concept_url
                        };
                    case 'string-mc':
                        return v.map(smc => {
                            return {
                                id: smc.id,
                                concept_url: smc.concept_url
                            };
                        });
                    case 'table':
                        return v.map(row => {
                            for(let k in row) {
                                const col = row[k];
                                const aid = entry.attribute.id;
                                const tattr = attributes.find(a => a.id == aid);
                                const attr = tattr.columns[k];
                                // return necessary fields only
                                switch(attr.datatype) {
                                    case 'string-sc':
                                        row[k] = {
                                            id: col.id,
                                            concept_url: col.concept_url
                                        };
                                        break;
                                    case 'entity':
                                        row[k] = {
                                            id: col.id,
                                            name: col.name
                                        };
                                        break;
                                    default:
                                        row[k] = col;
                                        break;
                                }
                            }
                            return row;
                        });
                    default:
                        return entry.value;
                }
            },
            setModificationFields(entity) {
                if(!this.selectedEntity && !this.selectedEntity.id) return;

                this.selectedEntity.lasteditor = entity.lasteditor;
                this.selectedEntity.updated_at = entity.updated_at;
            },
            handleModeration(event) {
                const action = event.action;
                const aid = event.attribute_id;
                const eid = this.selectedEntity.id;
                const data = {
                    action: action
                };
                $http.patch(`entity/${eid}/attribute/${aid}/moderation`, data).then(response => {
                    Vue.delete(this.selectedEntity.data[aid], 'moderation_state');

                    if(action == 'accept') {
                        Vue.delete(this.selectedEntity.data[aid], 'original_value');
                    } else if(action == 'deny') {
                        Vue.set(this.selectedEntity.data[aid], 'value', this.selectedEntity.data[aid].original_value);
                        Vue.delete(this.selectedEntity.data[aid], 'original_value');
                    }
                });
            },
            updateDependencyCounter(event) {
                this.hiddenAttributes = event.counter;
            },
            dependencyInfoHoverOver(event) {
                if(this.dependencyInfoHovered) {
                    return;
                }
                this.dependencyInfoHovered = true;
                $('#dependency-info').popover({
                    placement: 'bottom',
                    animation: true,
                    html: false,
                    content: this.$tc('main.entity.attributes.hidden', this.hiddenAttributes, {
                        cnt: this.hiddenAttributes
                    })
                });
                $('#dependency-info').popover('show');
            },
            dependencyInfoHoverOut(event) {
                this.dependencyInfoHovered = false;
                $('#dependency-info').popover('dispose');
            },
            setReferenceAttribute(aid) {
                this.referenceAttribute = aid;
            },
            showMetadata(attribute) {
                this.$router.push({
                    append: true,
                    name: 'entityrefs',
                    params: {
                        aid: attribute.id
                    },
                    query: this.$route.query
                });
            },
            hasReferenceGroup: function(group) {
                if(!this.selectedEntity.references) return false;
                if(!Object.keys(this.selectedEntity.references).length) return false;
                if(!this.selectedEntity.references[group]) return false;
                let count = Object.keys(this.selectedEntity.references[group]).length > 0;
                return count > 0;
            },
            resetFlags() {
                this.$validator.fields.items.forEach(field => {
                    field.reset();
                });
            },
        },
        data() {
            return {
                newEntityName: '',
                entityHeaderHovered: false,
                dataLoaded: false,
                dependencyInfoHovered: false,
                hiddenAttributes: 0,
                referenceAttribute: null
            }
        },
        computed: {
            isFormDirty() {
                return Object.keys(this.fields).some(key => this.fields[key].dirty) && !this.errors.any();
            },
            hasData() {
                return this.dataLoaded &&
                    !!this.selectedEntity &&
                    !!this.selectedEntity.attributes &&
                    !!this.selectedEntity.selections
            },
            colorStyles() {
                const colors = this.$getEntityColors(this.selectedEntity.entity_type_id, 0.75);
                return {
                    color: colors.backgroundColor
                };
            },
            attributeReferences() {
                let data = {
                    refs: [],
                    value: {},
                    attribute: {}
                };
                if(this.referenceAttribute) {
                    const attribute = this.selectedEntity.attributes.find(a => a.id == this.referenceAttribute);
                    if(!attribute) return data;
                    data.refs = this.hasReferenceGroup(attribute.thesaurus_url) ? this.selectedEntity.references[attribute.thesaurus_url] : [];
                    data.value = this.selectedEntity.data[this.referenceAttribute];
                    data.attribute = attribute;
                }
                return data;
            }
        },
        watch: {
            isFormDirty(newDirty, oldDirty) {
                if(newDirty != oldDirty) {
                    this.$emit('detail-updated', {
                        isDirty: newDirty,
                        onDiscard: newDirty ? this.saveEntity : entity => {}
                    });
                }
            }
        }
    }
</script>
