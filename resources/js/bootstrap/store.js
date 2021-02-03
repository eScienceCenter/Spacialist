import { createStore } from 'vuex';

import { sortTree, Node } from '../helpers/tree.js';
import { can } from '../helpers/helpers.js';
import { getEntityData } from '../api.js';

export const store = createStore({
    state() {
        return {
            attributes: [],
            entityTypeAttributes: {},
            bibliography: [],
            concepts: {},
            deletedUsers: [],
            entity: {},
            entityTypes: {},
            entities: {},
            file: {},
            permissions: [],
            preferences: {},
            roles: [],
            tree: [],
            user: {},
            users: [],
        }
    },
    mutations: {
        setAttributes(state, data) {
            state.attributes = data;
        },
        setBibliography(state, data) {
            state.bibliography = data;
        },
        addEntity(state, n) {
            state.entities[n.id] = n;
            state.tree.push(n);
        },
        setConcepts(state, data) {
            state.concepts = data;
        },
        setEntityTypes(state, data) {
            for(let k in data) {
                const et = data[k];
                state.entityTypeAttributes[et.id] = et.attributes.slice();
                delete et.attributes;
            }
            state.entityTypes = data;
        },
        sortTree(state, sort) {
            sortTree(sort.by, sort.dir, state.tree);
        },
        setPreferences(state, data) {
            state.preferences = data;
        },
        setRoles(state, data) {
            state.roles = data;
        },
        setPermissions(state, data) {
            state.permissions = data;
        },
        setUsers(state, data) {
            state.users = data.active;
            state.deletedUsers = data.deleted;
        },
        setUser(state, data) {
            state.user = data;
        },
        setEntity(state, data) {
            state.entity = data;
        },
        setFile(state, data) {
            state.file = data;
        }
    },
    actions: {
        setBibliography({commit}, data) {
            commit('setBibliography', data);
        },
        setRoles({commit}, data) {
            commit('setRoles', data.roles);
            commit('setPermissions', data.permissions);
        },
        setUsers({commit}, data) {
            commit('setUsers', data);
        },
        sortTree({commit}, sort) {
            commit('sortTree', sort)
        },
        async getEntity({commit, state}, entityId) {
            let entity = state.entities[entityId];
            console.log(entity);
            if(!can('view_concept_props')) {
                const hiddenEntity = {
                    ...entity,
                    data: {},
                    attributes: [],
                    selections: {},
                    dependencies: [],
                    references: [],
                    comments: [],
                };
                commit('setEntity', hiddenEntity);
            } else {
                entity.data = await getEntityData(entityId);
                commit('setEntity', entity)
                
                return;

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
                    this.setEntityView();
                }));
            }
        },
        setInitialEntities({commit, state}, data) {
            state.tree = [];
            state.entities = {};

            data.forEach(e => {
                const n = new Node(e);
                commit('addEntity', n);
            });
            sortTree('rank', 'asc', state.tree);
        },
        setEntityTypes({commit, state}, data) {
            state.entityTypes = {};
            state.entityTypeAttributes = {};
            commit('setEntityTypes', data);
        },
        setAttributes({commit, state}, data) {
            state.attributes = [];
            commit('setAttributes', data);
        },
    },
    getters: {
        attributes: state => {
            return state.attributes;
        },
        bibliography: state => {
            return state.bibliography;
        },
        concepts: state => {
            return state.concepts;
        },
        entityTypes: state => {
            return state.entityTypes;
        },
        entityTypeAttributes: state => id => {
            return state.entityTypeAttributes[id];
        },
        tree: state => {
            return state.tree;
        },
        preferenceByKey: state => key => {
            return state.preferences[key];
        },
        preferences: state => {
            return state.preferences;
        },
        roles: state => noPerms => {
            return noPerms ? state.roles.map(r => {
                // Remove permissions from role
                let {permissions, ...role} = r;
                return role;
            }) : state.roles;
        },
        permissions: state => {
            return state.permissions;
        },
        allUsers: state => {
            return [
                ...state.users,
                ...state.deletedUsers
            ];
        },
        users: state => {
            return state.users;
        },
        deletedUsers: state => {
            return state.deletedUsers;
        },
        user: state => {
            return state.user;
        },
        isLoggedIn: state => {
            return !!state.user;
        },
        entity: state => {
            return state.entity;
        },
        file: state => {
            return state.file;
        },
    }
});

export function useStore() {
    return store;
}

export default store;