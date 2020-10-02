<template>
    <div>
        <div class="input-group">
            <div class="input-group-prepend">
                <button type="button" class="btn btn-outline-secondary" @click="toggleList()">
                    <div v-show="!expanded">
                        <i class="fas fa-fw fa-caret-up"></i>
                        <span v-if="entries.length">
                            ({{entries.length}})
                        </span>
                    </div>
                    <div v-show="expanded">
                        <i class="fas fa-fw fa-caret-down"></i>
                    </div>
                </button>
            </div>
            <input type="text" class="form-control" :disabled="disabled" v-model="input" />
            <div class="input-group-append" v-if="!disabled">
                <button type="button" class="btn btn-success" @click="addListEntry()">
                    <i class="fas fa-fw fa-plus"></i>
                </button>
            </div>
        </div>
        <ol class="mt-2" v-if="expanded && entries.length">
            <li v-for="(l, i) in entries">
                {{ l }}
                <a href="#" class="text-danger" v-if="!disabled" @click="removeListEntry(i)">
                    <i class="fas fa-fw fa-trash"></i>
                </a>
            </li>
        </ol>
    </div>
</template>

<script>
    export default {
        $_veeValidate: {
            // value getter
            value () {
                return this.$el.value;
            },
            // name getter
            name () {
                return this.name;
            }
        },
        props: {
            name: String,
            entries: {
                type: Array,
                default: _ => new Array(),
            },
            disabled: {
                type: Boolean,
            },
            onChange: {
                type: Function,
                required: true,
            }
        },
        mounted () {
            this.$el.value = this.entries;
        },
        methods: {
            onInput(value) {
                this.$emit('input', value);
                this.onChange(value);
            },
            addListEntry() {
                if(!this.entries) {
                    this.entries = [];
                }
                this.entries.push(this.input);
                this.input="";
                this.onInput(this.entries);
            },
            removeListEntry(index) {
                this.entries.splice(index, 1);
                this.onInput(this.entries);
            },
            toggleList() {
                this.expanded = !this.expanded;
            },
        },
        data () {
            return {
                input: "",
                expanded: false
            }
        }
    }
</script>
