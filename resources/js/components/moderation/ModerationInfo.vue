<template>
    <div>
        <span v-if="deleted" v-html="$t('main.role.moderation.deleted_by', {name: data.lasteditor, ts: data.updated_at })"></span>
        <span v-else v-html="$t('main.role.moderation.modified_by', {name: data.lasteditor, ts: data.updated_at })"></span>
    </div>
</template>

<script>
    export default {
        props: {
            data: {
                required: true,
                type: Object,
            }
        },
        mounted() {},
        methods: {
        },
        data() {
            return {
            }
        },
        computed: {
            deleted() {
                return this.data.moderation_state == 'pending-delete'
            }
        }
    }
</script>
