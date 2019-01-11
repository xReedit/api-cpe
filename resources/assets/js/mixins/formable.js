import http from '../helpers/http'

export const formable = {
    data () {
        return {
            errors: {},
            form: {},
            submit_loading: false,
        }
    },
    methods: {
        _tables() {
            return new Promise((resolve) => {
                let url = `/${this.resource}/tables/`
                http.get(url)
                    .then(response => {
                        resolve(response.data)
                    })
                    .catch(error => {
                        console.log(error)
                    })
            })
        },
        _create() {
            return new Promise((resolve) => {
                let url = `/${this.resource}/create/${this.recordId}`
                http.get(url)
                    .then(response => {
                        this.form = response.data.form
                        resolve(response)
                    }).catch(error => {
                    console.log(error)
                })
            })
        },
        _submit() {
            return new Promise((resolve, reject) => {
                this.submit_loading = true
                http.post(`/${this.resource}`, this.form)
                    .then(response => {
                        if (response.data.success) {
                            this.$message.success(response.data.message)
                            resolve(response.data.data)
                        } else {
                            this.$message.error(response.data.message)
                        }
                    })
                    .catch(error => {
                        if (error.response.status === 422) {
                            this.errors = error.response.data.message
                        } else {
                            this.$message.error(error.response.data.message)
                        }
                    })
                    .then(() => {
                        this.submit_loading = false
                    })
            })
        },
    }
};