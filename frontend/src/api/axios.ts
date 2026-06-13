import axios from "axios";
import { queryClient } from "../lib/queryClient";
import { useAuthStore } from "../store/authStore";
import { useHttpErrorStore } from "../store/httpErrorStore";
import envVar from "../utils/envReader";
import { generateRequestId } from "../utils/requestId";

type RequestMeta = {
  requestId: string;
  startedAt: number;
};

const api = axios.create({
  baseURL: envVar.API_URL,
});

const appBasePath = import.meta.env.BASE_URL || "/";

api.interceptors.request.use((config) => {
  const requestId = generateRequestId();
  (config as typeof config & { metadata?: RequestMeta }).metadata = {
    requestId,
    startedAt: Date.now(),
  };

  config.headers["X-Request-Id"] = requestId;

  const token = useAuthStore.getState().token;
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }

  return config;
});

api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (!error?.response) {
      useHttpErrorStore.getState().actions.setError("network_error");
      return Promise.reject(error);
    }

    const statusCode = Number(error.response.status ?? 0);
    if (statusCode === 403) {
      useHttpErrorStore.getState().actions.setError("forbidden", statusCode);
    } else if (statusCode === 404) {
      useHttpErrorStore.getState().actions.setError("not_found", statusCode);
    } else if (statusCode >= 500) {
      useHttpErrorStore.getState().actions.setError("internal_server_error", statusCode);
    }

    if (error.response && error.response.status === 401) {
      queryClient.clear();
      const { logout } = useAuthStore.getState().actions;
      logout();
      window.location.href = `${appBasePath}login`;
    }

    return Promise.reject(error);
  },
);

export default api;
