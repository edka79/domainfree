import axios from 'axios'

export function getFavorite(p = '') {
    return axios.get(`/favorite${p}`);
}
export function getFavoriteCount(p = '1') {
    return axios.put(`/favorite/${p}`);
}
export function setFavorite(params) {
    return axios.post(`/favorite`, params);
}

export function getNobody(p = '') {
    return axios.get(`/nobody${p}`);
}

export function searching(p = '') {
    return axios.get(`/searching${p}`);
}
