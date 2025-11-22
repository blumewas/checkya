import Axios from 'axios';

export const axios = Axios.create();

if (typeof window !== 'undefined') {
  const token = document.head.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
  axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
  axios.defaults.headers.common.Accept = 'application/json';
  axios.defaults.headers.patch['Content-Type'] = 'application/json';
  axios.defaults.headers.post['Content-Type'] = 'application/json';

  if (token) {
    axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
  }
}

axios.interceptors.response.use(
  (response) => response.data,
  (error) => Promise.reject(error)
);
