/* global axios, Promise */

import app from "./app.js";

// Todas las solicitudes se ejecutan por POST
export default {
  submitSimpleRequest: async uri => {
    try {
      const url = app.url(uri);
      const params = {headers: app.ajaxHeaders};
      const response = await axios.post(url, null, params);
      return Promise.resolve(response.data);
    } catch (err) {
      if (err.response && err.response.data && err.response.data.message)
        return Promise.reject(err.response.data.message);

      if (err.response && err.response.data)
        return Promise.reject(err.response.data);

      if (err.response)
        return Promise.reject(err.response);

      return Promise.reject(err);
    }
  },

  submitData: async (uri, data) => {
    try {
      const url = app.url(uri);
      const params = {headers: app.ajaxHeaders};
      const response = await axios.post(url, data, params);
      return Promise.resolve(response.data);
    } catch (err) {
      if (err.response && err.response.data && err.response.data.message)
        return Promise.reject(err.response.data.message);

      if (err.response && err.response.data)
        return Promise.reject(err.response.data);

      if (err.response)
        return Promise.reject(err.response);

      return Promise.reject(err);
    }
  },

  submitForm: async form => {
    try {
      const data = new FormData(form);
      const response = await axios.post(form.action, data);
      return Promise.resolve(response.data);
    } catch (err) {
      if (err.response && err.response.data && err.response.data.message)
        return Promise.reject(err.response.data.message);

      if (err.response && err.response.data)
        return Promise.reject(err.response.data);

      if (err.response)
        return Promise.reject(err.response);

      return Promise.reject(err);
    }
  }
}