<template>
    <div class="field">
        <label class="label" :for="cfor" v-if="label !== ''">{{ label }} <slot name="buttonAction"></slot></label>
        <div class="control">
            <slot></slot>
        </div>
        <p class="help is-danger" v-if="showError">{{ msgError }}</p>
    </div>
</template>
<script type="text/javascript">
    export default {
        props: {
            label: {
                required: false,
                type: String
            },
            cfor: {
                required: false,
                type: String
            },
            errors: {
                required: false,
            },
            errorField: {
                type: String,
                required: false,
            }
        },
        data() {
            return {
                showError: false,
                msgError: null,
            }
        },
        watch: {
            errors: function() {
                let error_array = this.errors[this.errorField]
                if (error_array) {
                    this.msgError = error_array[0]
                    this.showError = true
                } else {
                    this.msgError = null
                    this.showError = false
                }
            }
        }
    }
</script>