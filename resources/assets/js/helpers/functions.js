import http from './http'

export function get(url, query) {
    return new Promise((resolve) => {
        http.get(url, {
            params: query
        })
            .then(response => {
                resolve(response.data);
            })
            .catch(error => {
                console.log(error)
            })
    });
}

export function post(url, data) {
    return new Promise((resolve, reject) => {
        http.post(url, data)
            .then(response => {
                resolve(response.data);
            })
            .catch(error => {
                reject(error)
            })
    });
}