import http from '../helpers/http'

export const deletable = {
    methods: {
        _destroy(id) {
            return new Promise((resolve) => {
                let url = `/${this.resource}/${id}`
                this.$confirm(this.$t('actions.destroy.question'), this.$t('actions.destroy.title'), {
                    confirmButtonText: this.$t('buttons.continue'),
                    cancelButtonText: this.$t('buttons.cancel'),
                    type: 'warning'
                }).then(() => {
                    http.delete(url)
                        .then(res => {
                            if(res.data.success) {
                                this.$message.success(this.$t('actions.destroy.success'))
                                resolve()
                            }
                        })
                        .catch(error => {
                            if (error.response.status === 500) {
                                this.$message.error(this.$t('actions.destroy.error'));
                            } else {
                                console.log(error.response.data.message)
                            }
                        })
                }).catch(error => {
                    console.log(error)
                });
            })
        },
    }
}