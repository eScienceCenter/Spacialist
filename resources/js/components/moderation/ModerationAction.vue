<template>
    <div class="">
        <div class="col-md-9 offset-md-3" v-if="canModerate">
            <div class="my-2 d-flex flex-row justify-content-between">
                <moderation-info :data="value"></moderation-info>
                <div class="dropdown">
                    <span id="moderation-action-menu" class="clickable" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-fw fa-ellipsis-h"></i>
                    </span>
                    <div class="dropdown-menu" aria-labelledby="moderation-action-menu">
                        <a class="dropdown-item" href="#" @click.prevent="handleModeration('accept')">
                            <i class="fas fa-fw fa-user-check text-success"></i> {{ $t('main.role.moderation.accept_changes') }}
                        </a>
                        <a class="dropdown-item" href="#" @click.prevent="handleModeration('deny')">
                            <i class="fas fa-fw fa-user-times text-danger"></i> {{ $t('main.role.moderation.deny_changes') }}
                        </a>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-info" @click="toggleModerationData()">
                <i class="fas fa-fw fa-paste"></i>
                <span v-if="!showingOriginalValue">
                    {{ $t('main.role.moderation.view_original_data') }}
                </span>
                <span v-else>
                    {{ $t('main.role.moderation.view_modified_data') }}
                </span>
            </button>
        </div>
        <div v-else-if="requireModeration" class="col-md-9 offset-md-3 mt-2">
            <p class="alert alert-warning m-0">
                {{ $t('main.role.moderation.locked_state_info') }}
            </p>
        </div>
    </div>
</template>

<script>
    import ModerationInfo from './ModerationInfo.vue';

    export default {
        props: {
            canModerate: {
                required: false,
                type: Boolean,
                default: false
            },
            requireModeration: {
                required: false,
                type: Boolean,
                default: false
            },
            element: {
                required: true,
                type: Object,
            },
            value: {
                required: true,
                type: Object
            }
        },
        components: {
            'moderation-info': ModerationInfo
        },
        mounted() {},
        methods: {
            handleModeration(action) {
                if(!this.canModerate) return;

                this.$emit('handle-moderation', {
                    action: action,
                    id: this.element.id
                });
            },
            toggleModerationData() {
                this.showingOriginalValue = !this.showingOriginalValue;
                this.$emit('handle-data-toggle', {
                    id: this.element.id
                });
            }
        },
        data() {
            return {
                showingOriginalValue: false
            }
        },
        computed: {

        }
    }
</script>
