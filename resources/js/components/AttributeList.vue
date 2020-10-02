<template>
    <div class="pr-1">
        <draggable
            class="h-100"
            v-bind="dragOpts"
            v-model="localAttributes"
            :class="{'drag-container': !disableDrag}"
            :clone="clone"
            :move="move"
            @add="added"
            @end="dropped"
            @start="dragged">
            <div class="form-group row" :class="{'disabled not-allowed-handle': attribute.isDisabled, 'alert-danger rounded mx-1 py-2': canModerate(attribute.id)}" v-for="(attribute, i) in localAttributes" @mouseenter="onEnter(i)" @mouseleave="onLeave(i)" v-show="!hiddenByDependency[attribute.id]">
                <label class="col-form-label col-md-3 d-flex flex-row justify-content-between text-break" :for="`attribute-${attribute.id}`" :class="{'copy-handle': isSource&&!attribute.isDisabled, 'not-allowed-handle text-muted': attribute.isDisabled || modificationLocked(attribute.id)}">
                    <div v-show="hoverState[i]">
                        <a v-show="onReorder" href="" @click.prevent="" class="reorder-handle" data-toggle="popover" :data-content="$t('global.resort')" data-trigger="hover" data-placement="bottom">
                            <i class="fas fa-fw fa-sort text-secondary"></i>
                        </a>
                        <button v-show="onEdit" class="btn btn-info btn-fab rounded-circle" @click="onEdit(attribute)" data-toggle="popover" :data-content="$t('global.edit')" data-trigger="hover" data-placement="bottom">
                            <i class="fas fa-fw fa-xs fa-edit" style="vertical-align: 0;"></i>
                        </button>
                        <button v-show="onRemove" class="btn btn-danger btn-fab rounded-circle" @click="onRemove(attribute)" data-toggle="popover" :data-content="$t('global.remove')" data-trigger="hover" data-placement="bottom">
                            <i class="fas fa-fw fa-xs fa-times" style="vertical-align: 0;"></i>
                        </button>
                        <button v-show="onDelete" class="btn btn-danger btn-fab rounded-circle" @click="onDelete(attribute)" data-toggle="popover" :data-content="$t('global.delete')" data-trigger="hover" data-placement="bottom">
                            <i class="fas fa-fw fa-xs fa-trash" style="vertical-align: 0;"></i>
                        </button>
                    </div>
                    <span class="text-right col">
                        {{ $translateConcept(attribute.thesaurus_url) }}:
                    </span>
                    <sup class="clickable" v-if="onMetadata" @click="onMetadata(attribute)">
                        <span :class="getCertaintyClass(localValues[attribute.id].certainty)">
                            <i class="fas fa-fw fa-exclamation"></i>
                        </span>
                        <span v-if="localValues[attribute.id].certainty_description">
                            <i class="fas fa-fw fa-comment"></i>
                        </span>
                        <span v-if="metadataAddon(attribute.thesaurus_url)">
                            <i class="fas fa-fw fa-bookmark"></i>
                        </span>
                    </sup>
                </label>
                <div :class="expanded[attribute.id]">
                    <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-if="attribute.datatype == 'string'" type="text" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model="localValues[attribute.id].value" @blur="checkDependency(attribute.id)" />
                    <input class="form-control-plaintext" v-else-if="attribute.datatype == 'serial'" type="text" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" readonly v-model="localValues[attribute.id].value" @blur="checkDependency(attribute.id)" />
                    <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-else-if="attribute.datatype == 'double'" type="number" step="any" min="0" placeholder="0.0" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model.number="localValues[attribute.id].value" @blur="checkDependency(attribute.id)" />
                    <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-else-if="attribute.datatype == 'integer'" type="number" step="1" placeholder="0" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model.number="localValues[attribute.id].value" @blur="checkDependency(attribute.id)" />
                    <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-else-if="attribute.datatype == 'boolean'" type="checkbox" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model="localValues[attribute.id].value" @change="checkDependency(attribute.id)" />
                    <textarea class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-else-if="attribute.datatype == 'stringf'" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model="localValues[attribute.id].value" @blur="checkDependency(attribute.id)"></textarea>
                    <div v-else-if="attribute.datatype == 'percentage'" class="d-flex">
                        <input class="custom-range" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" type="range" step="1" min="0" max="100" value="0" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" v-model="localValues[attribute.id].value" @mouseup="checkDependency(attribute.id)"/>
                        <span class="ml-3">{{ localValues[attribute.id].value }}%</span>
                    </div>
                    <div v-else-if="attribute.datatype == 'geography'">
                        <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" type="text" :id="'attribute-'+attribute.id" :name="'attribute-'+attribute.id" v-validate="" :placeholder="$t('main.entity.attributes.add-wkt')" v-model="localValues[attribute.id].value" @blur="checkDependency(attribute.id)" />
                        <button type="button" class="btn btn-outline-secondary mt-2" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" @click="openGeographyModal(attribute.id)">
                            <i class="fas fa-fw fa-map-marker-alt"></i> {{ $t('main.entity.attributes.open-map') }}
                        </button>
                    </div>
                    <div v-else-if="attribute.datatype == 'entity'">
                        <entity-search
                            v-validate=""
                            :id="'attribute-'+attribute.id"
                            :name="'attribute-'+attribute.id"
                            :on-select="selection => setEntitySearchResult(selection, attribute.id)"
                            :value="localValues[attribute.id].name">
                        </entity-search>
                    </div>
                    <date-picker
                        class="w-100"
                        v-else-if="attribute.datatype == 'date'"
                        v-validate=""
                        :id="`attribute-${attribute.id}`"
                        :disabled="attribute.isDisabled || modificationLocked(attribute.id)"
                        :disabled-date="(date) => date > new Date()"
                        :input-class="'form-control'"
                        :max-date="new Date()"
                        :name="`attribute-${attribute.id}`"
                        :show-week-number="true"
                        :value="localValues[attribute.id].value"
                        :value-type="'date'"
                        @input="setDateValue($event, attribute.id)">
                        <template v-slot:icon-calendar>
                            <i class="fas fa-fw fa-calendar-alt"></i>
                        </template>
                        <template v-slot:icon-clear>
                            <i class="fas fa-fw fa-times"></i>
                        </template>
                    </date-picker>
                    <div v-else-if="attribute.datatype == 'string-mc'">
                        <multiselect
                            label="concept_url"
                            track-by="id"
                            v-model="localValues[attribute.id].value"
                            v-validate=""
                            :allowEmpty="true"
                            :closeOnSelect="false"
                            :customLabel="translateLabel"
                            :disabled="attribute.isDisabled || modificationLocked(attribute.id)"
                            :hideSelected="true"
                            :multiple="true"
                            :options="localSelections[attribute.id] || []"
                            :name="'attribute-'+attribute.id"
                            :placeholder="$t('global.select.placehoder')"
                            :select-label="$t('global.select.select')"
                            :deselect-label="$t('global.select.deselect')"
                            @input="(value, id) => checkDependency(attribute.id)">
                        </multiselect>
                    </div>
                    <div v-else-if="attribute.datatype == 'string-sc'">
                        <multiselect
                            label="concept_url"
                            track-by="id"
                            v-model="localValues[attribute.id].value"
                            v-validate=""
                            :allowEmpty="true"
                            :closeOnSelect="true"
                            :customLabel="translateLabel"
                            :disabled="attribute.isDisabled || modificationLocked(attribute.id)"
                            :hideSelected="true"
                            :loading="dd.loading[attribute.id]"
                            :multiple="false"
                            :options="dd.selections[attribute.id] || []"
                            :name="'attribute-'+attribute.id"
                            :placeholder="$t('global.select.placehoder')"
                            :select-label="$t('global.select.select')"
                            :deselect-label="$t('global.select.deselect')"
                            @open="getOptions(attribute)"
                            @input="(value, id) => checkDependency(attribute.id)">
                        </multiselect>
                    </div>
                    <div v-else-if="attribute.datatype == 'list'">
                        <list :entries="localValues[attribute.id].value" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" :on-change="value => onChange(null, value, attribute.id)" :name="'attribute-'+attribute.id" v-validate="" />
                    </div>
                    <epoch v-else-if="attribute.datatype == 'epoch' || attribute.datatype == 'timeperiod'" :name="'attribute-'+attribute.id" :on-change="(field, value) => onChange(field, value, attribute.id)" :value="localValues[attribute.id].value" :epochs="localSelections[attribute.id]" :type="attribute.datatype" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-validate=""/>
                    <div v-else-if="attribute.datatype == 'dimension'">
                        <dimension :name="'attribute-'+attribute.id" :on-change="(field, value) => onChange(field, value, attribute.id)" :value="localValues[attribute.id].value" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-validate=""/>
                    </div>
                    <tabular v-else-if="attribute.datatype == 'table'" :name="'attribute-'+attribute.id" :on-change="(field, value) => onChange(field, value, attribute.id)" :value="localValues[attribute.id].value" :selections="localSelections" :attribute="attribute" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" @expanded="onAttributeExpand" v-validate=""/>
                    <div v-else-if="attribute.datatype == 'sql'">
                        <div v-if="isArray(localValues[attribute.id].value)">
                            <div class="table-responsive">
                                <table class="table table-striped table-hovered table-sm">
                                    <thead class="thead-light">
                                        <tr>
                                            <th v-for="(columnNames, index) in localValues[attribute.id].value[0]">
                                                {{ $translateConcept(index) }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(row, $index) in localValues[attribute.id].value">
                                            <td v-for="column in row">
                                                {{ $translateConcept(column) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div v-else>
                            {{ localValues[attribute.id].value }}
                        </div>
                    </div>
                    <iconclass v-else-if="attribute.datatype == 'iconclass'" :name="`attribute-${attribute.id}`" @input="updateValue($event, attribute.id)" :value="localValues[attribute.id].value" :attribute="attribute" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-validate=""></iconclass>
                    <input class="form-control" :disabled="attribute.isDisabled || modificationLocked(attribute.id)" v-else type="text" :id="'attribute-'+attribute.id" v-model="localValues[attribute.id].value"  :name="'attribute-'+attribute.id" v-validate="" @blur="checkDependency(attribute.id)"/>
                </div>
                <moderation-action
                    class="w-100"
                    :can-moderate="canModerate(attribute.id)"
                    :require-moderation="needsModeration(attribute.id)"
                    :element="attribute"
                    :value="localValues[attribute.id]"
                    @handle-moderation="handleModeration"
                    @handle-data-toggle="handleDataToggle">
                </moderation-action>
            </div>
        </draggable>
        <modal :name="'geography-place-modal-'+uniqueId" width="80%" height="80%">
            <div class="modal-content h-100">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $t('main.entity.attributes.set-location') }}</h5>
                    <button type="button" class="close" aria-label="Close" @click="hideGeographyModal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body d-flex flex-column flex-grow-1 overflow-hidden">
                    <ol-map class="flex-grow-1 overflow-hidden"
                        :epsg="{epsg: '4326'}"
                        :layers="wktLayers"
                        :init-wkt="initialGeoValues"
                        :init-projection="'EPSG:4326'"
                        :on-deleteend="onGeoFeaturesDeleted"
                        :on-drawend="onGeoFeatureAdded"
                        :on-modifyend="onGeoFeaturesUpdated"
                        :reset="true">
                    </ol-map>
                    <div class="mt-2">
                        WKT: <pre class="m-0"><code>{{ newGeoValue }}</code></pre>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-success"     @click="setGeography('attribute-'+selectedAttribute)">
                        {{ $t('global.set') }}
                    </button>
                    <button type="button" class="btn btn-outline-secondary"     @click="hideGeographyModal">
                        {{ $t('global.close') }}
                    </button>
                </div>
            </div>
        </modal>
    </div>
</template>

<script>
    import draggable from 'vuedraggable';
    import { mapFields } from 'vee-validate';

    import Dimension from './Dimension.vue';
    import Epoch from './Epoch.vue';
    import List from './List.vue';
    import Tabular from './Tabular.vue';
    import Iconclass from './Iconclass.vue';

    import ModerationAction from './moderation/ModerationAction.vue';

    export default {
        props: {
            attributes: {
                required: true,
                type: Array
            },
            dependencies: {
                required: false,
                type: Object,
                default: _ => new Object()
            },
            disableDrag: {
                required: false,
                type: Boolean,
                default: false
            },
            group: { // required if onReorder is set // TODO
                required: false,
                type: String
            },
            isSource: {
                required: false,
                type: Boolean,
                default: false
            },
            metadataAddon: {
                required: false,
                type: Function,
                default: () => false
            },
            onAdd: {
                required: false,
                type: Function
            },
            onDelete: {
                required: false,
                type: Function
            },
            onEdit: {
                required: false,
                type: Function
            },
            onMetadata: { // Sources modal
                required: false,
                type: Function
            },
            onRemove: {
                required: false,
                type: Function
            },
            onReorder: {
                required: false,
                type: Function
            },
            selections: {
                required: true,
                type: Object
            },
            showInfo: { // shows parent on hover
                required: false,
                type: Boolean
            },
            values: {
                required: true,
                type: Object
            }
        },
        components: {
            draggable,
            'dimension': Dimension,
            'epoch': Epoch,
            'list': List,
            'tabular': Tabular,
            'iconclass': Iconclass,
            'moderation-action': ModerationAction
        },
        inject: ['$validator'],
        beforeMount() {
            // Enable popovers
            $(function () {
                $('[data-toggle="popover"]').popover()
            });
        },
        mounted() {
            this.attributes.forEach(a => this.checkDependency(a.id));
        },
        methods: {
            handleModeration(event) {
                const action = event.action;
                const aid = event.id;

                // event handler in parent element expects modified value
                // to be in .value and original value in .original_value
                // thus we have to toggle if original value is in .value
                if(this.showingOriginalValue[aid]) {
                    this.toggleModerationData(aid);
                }

                this.$emit('handle-moderation', {
                    action: action,
                    attribute_id: aid
                });
            },
            handleDataToggle(event) {
                this.toggleModerationData(event.id);
            },
            toggleModerationData(aid) {
                const tmp = this.localValues[aid].value;
                Vue.set(this.localValues[aid], 'value', this.localValues[aid].original_value);
                Vue.set(this.localValues[aid], 'original_value', tmp);
                Vue.set(this.showingOriginalValue, aid, !this.showingOriginalValue[aid]);
                // datepicker doesn't support this kind of value changing
                // thus update it by hand
                let attr = this.localAttributes.find(a => a.id == aid);
                if(attr.datatype == 'date') {
                    this.setDateValue(new Date(this.localValues[aid].value), aid, true);
                }
            },
            onEnter(i) {
                Vue.set(this.hovered, i, this.hoverEnabled);
            },
            onLeave(i) {
                Vue.set(this.hovered, i, false);
            },
            onChange(field, value, aid) {
                if(this.localValues[aid].value){
                    if(field == null) {
                        this.localValues[aid].value = [];
                        value.forEach(v => this.localValues[aid].value.push(v));
                    } else {
                        this.localValues[aid].value[field] = value;
                    }
                }
                this.checkDependency(aid);
            },
            updateValue(eventValue, aid) {
                this.localValues[aid].value = eventValue;
            },
            modificationLocked(aid) {
                return this.localValues[aid].moderation_state && this.localValues[aid].moderation_state.startsWith('pending');
            },
            needsModeration(aid) {
                return this.localValues[aid].moderation_state && this.localValues[aid].moderation_state.startsWith('pending') && this.$moderated();
            },
            canModerate(aid) {
                return this.localValues[aid].moderation_state && this.localValues[aid].moderation_state.startsWith('pending') && !this.$moderated();
            },
            onAttributeExpand(e) {
                Vue.set(this.expanded, e.id, e.state ? ['col-md-12'] : ['col-md-9']);
            },
            setDateValue(value, aid, noChecks = false) {
                const utcValue = this.getDateAsUTC(value);
                this.localValues[aid].value = utcValue;
                if(!noChecks) {
                    this.fields[`attribute-${aid}`].dirty = true;
                    this.checkDependency(aid);
                }
            },
            getDateAsUTC(dt) {
                const offset = dt.getTimezoneOffset() * (-1);
                let ms = Date.parse(dt.toUTCString());
                ms += (offset * 60 * 1000);
                return new Date(ms);
            },
            setEntitySearchResult(result, aid) {
                if(result) {
                    this.localValues[aid].value = result.id;
                } else {
                    this.localValues[aid].value = undefined;
                }
                this.fields[`attribute-${aid}`].dirty = true;
                this.checkDependency(aid);
            },
            getCertaintyClass(certainty) {
                let activeClasses = [];
                if(certainty <= 25) {
                    activeClasses.push('text-danger');
                } else if(certainty <= 50) {
                    activeClasses.push('text-warning');
                } else if(certainty <= 75) {
                    activeClasses.push('text-info');
                } else {
                    activeClasses.push('text-success');
                }
                return activeClasses;
            },
            getOptions(attribute) {
                if(attribute.root_attribute_id) {
                    const rootAttr = this.localAttributes.find(a => a.id == attribute.root_attribute_id);
                    if(!rootAttr) {
                        Vue.set(this.dd.selections, attribute.id, []); // TODO set info
                        return;
                    }
                    const rootValue = this.localValues[attribute.root_attribute_id];
                    if(!rootValue || !rootValue.value) {
                        Vue.set(this.dd.selections, attribute.id, []); // TODO set info
                        return;
                    }
                    const rootId = rootValue.value.id;
                    // if value of root value has not changed since last time,
                    // keep current selection
                    if(this.dd.idCache[attribute.id] == rootId) {
                        return;
                    }
                    this.dd.loading[attribute.id] = true;
                    $httpQueue.add(() => $http.get(`search/selection/${rootValue.value.id}`).then(response => {
                        this.dd.loading[attribute.id] = false;
                        this.dd.idCache[attribute.id] = rootId;
                        Vue.set(this.dd.selections, attribute.id, response.data);
                    }));
                } else {
                    const selection = this.localSelections[attribute.id] || [];
                    Vue.set(this.dd.selections, attribute.id, selection);
                }
            },
            checkDependency(aid) {
                if(!this.dependencies) return;
                if(!this.dependencies[aid]) return;
                let hides = {};
                const deps = this.dependencies[aid];
                deps.forEach(d => {
                    // return = continue in forEach
                    if(hides[d.dependant]) return;
                    // Hide if current value does not match the
                    // dependency
                    hides[d.dependant] = !this.matchDependency(aid, this.localValues[aid], d.operator, d.value);
                });
                for(let k in hides) {
                    Vue.set(this.hiddenByDependency, k, hides[k]);
                }
                this.$emit('attr-dep-change', {
                    counter: this.hiddenAttributes
                });
            },
            matchDependency(attribute_id, attrValue, operator, depValue) {
                if(!attrValue || !attrValue.value) return false;
                const attr = this.localAttributes.find(function(a) {
                    return a.id == attribute_id;
                });
                if(!attr) return false;
                switch(attr.datatype) {
                    case 'string-mc':
                        for(let i=0; i<attrValue.value.length; i++) {
                            const v = attrValue.value[i];
                            if(this.evalEquation(v.concept_url, depValue, operator)) {
                                return true;
                            }
                        }
                        return false;
                        break;
                    case 'string-sc':
                        return this.evalEquation(attrValue.value.concept_url, depValue, operator);
                        break;
                    default:
                        return this.evalEquation(attrValue.value, depValue, operator);
                        break;
                }
            },
            evalEquation(a, b, op) {
                switch(op) {
                    case '<':
                        return a < b;
                    case '>':
                        return a > b;
                    case '=':
                        return a == b;
                }
                return false;
            },
            openGeographyModal(aid) {
                this.currentGeoValue = this.newGeoValue = this.localValues[aid].value;
                if(this.currentGeoValue) {
                    this.initialGeoValues = [
                        this.currentGeoValue
                    ];
                } else {
                    this.initialGeoValues = [];
                }
                this.selectedAttribute = aid;
                $http.get('map/layer?basic=1').then(response => {
                    this.wktLayers = {};
                    const bl = response.data.baselayers;
                    const ol = response.data.overlays;
                    bl.forEach(l => {
                        this.wktLayers[l.id] = l;
                    });
                    ol.forEach(l => {
                        this.wktLayers[l.id] = l;
                    });
                    this.$modal.show('geography-place-modal-'+this.uniqueId);
                });
            },
            hideGeographyModal() {
                this.$modal.hide('geography-place-modal-'+this.uniqueId);
            },
            onGeoFeaturesDeleted(features, wkt) {
                // We only have one possible feature.
                // Thus, if at least one gets deleted,
                // we reset newGeoValue
                if(wkt.length) {
                    this.newGeoValue = '';
                }
            },
            onGeoFeatureAdded(feature, wkt) {
                this.newGeoValue = wkt;
                return new Promise(r => r(feature));
            },
            onGeoFeaturesUpdated(features, wkt) {
                // We only have one possible feature.
                // Thus, if at least one is modified,
                // it has to be our current and we update it
                if(wkt.length) {
                    this.newGeoValue = wkt[0];
                }
            },
            setGeography(fieldname) {
                if(!!this.localValues[this.selectedAttribute]) {
                    this.localValues[this.selectedAttribute].value = this.newGeoValue;
                    this.fields[fieldname].dirty = true;
                }
                this.hideGeographyModal();
                this.currentGeoValue = undefined;
                this.newGeoValue = undefined;
                this.selectedAttribute = -1;
            },
            translateLabel(element, prop) {
                return this.$translateLabel(element, prop);
            },
            // Vue.Draggable methods
            clone(original) {
                return Object.assign({}, original);
            },
            dragged(event) {
                this.drag = true;
            },
            added(event) {
                let oldIndex = event.oldIndex;
                let newIndex = event.newIndex;
                this.onAdd(oldIndex, newIndex);
            },
            dropped(event) {
                this.drag = false;
                // return here if list is source only or source of drop
                if(this.isSource) return;
                let tgtList = event.to;
                let srcList = event.from;
                let oldIndex = event.oldIndex;
                let newIndex = event.newIndex;
                let isNew = tgtList != srcList;
                this.onReorder(oldIndex, newIndex);
            },
            move(event, originalEvent) {
                let src = event.draggedContext.element;
                let dst = event.relatedContext;
                let tgtList = event.to;
                let srcList = event.from;
                // Move is always allowed if not source or from other list
                if(!this.isSource && tgtList == srcList) {
                    return true;
                }
                let index = dst.list.findIndex(function(e) {
                    return src.id == e.id;
                });
                // move is only allowed, if dragged element is not part of the list
                return index == -1;
            },
            isArray(arr) {
                return Array.isArray(arr);
            }
        },
        data() {
            return {
                dd: {
                    selections: {},
                    idCache: {},
                    loading: {}
                },
                hiddenByDependency: {},
                hovered: [],
                expanded: {},
                uniqueId: Math.random().toString(36),
                selectedAttribute: -1,
                showingOriginalValue: {},
                initialGeoValues: [],
                wktLayers: {},
                currentGeoValue: '',
                newGeoValue: ''
            }
        },
        created() {
            for(let i=0; i<this.localAttributes.length; i++) {
                this.hovered.push(false);
                Vue.set(this.expanded, this.localAttributes[i].id, ['col-md-9']);
            }
        },
        computed: {
            importedAttributes: function() {
                return this.attributes.slice();
            },
            localAttributes: {
                get: function() {
                    return this.importedAttributes;
                },
                set: function(newValue) {
                    // return newValue;
                }
            },
            importedValues: function() {
                return Object.assign({}, this.values);
            },
            localValues: {
                get: function() {
                    return this.importedValues;
                },
                set: function(newValue) {
                    // return newValue;
                }
            },
            importedSelections: function() {
                return Object.assign({}, this.selections);
            },
            localSelections: {
                get: function() {
                    return this.importedSelections;
                },
                set function(newValue) {
                    // return newValue;
                }
            },
            hoverState: function() {
                return this.hovered;
            },
            dragOpts: function() {
                let opts = {};
                if(this.disableDrag) {
                    opts.disabled = true;
                    return opts;
                }
                if(this.group && !this.isSource) {
                    opts.group= {
                        name: this.group,
                        pull: false,
                        put: true
                    };
                } else if(this.group && this.isSource) {
                    opts.group = {
                        name: this.group,
                        pull: 'clone',
                        put: false
                    };
                    opts.sort = false;
                    opts.filter = '.disabled';
                }
                if(this.onReorder) {
                    opts.handle = '.reorder-handle';
                }
                return opts;
            },
            hiddenAttributes: function() {
                let cnt = 0;
                for(let k in this.hiddenByDependency) {
                    if(this.hiddenByDependency[k]) {
                        cnt++;
                    }
                }
                return cnt;
            },
            hoverEnabled: function() {
                return this.onReorder || this.onEdit  || this.onRemove || this.onDelete;
            }
        }
    }
</script>
