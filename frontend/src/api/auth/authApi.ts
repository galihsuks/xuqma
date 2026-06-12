import type { ApiResponse } from "../../interfaces/api";
import type { LoginResponse } from "../../interfaces/auth";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const authApi = {
  login: async (payload: {
    username: string;
    password: string;
  }): Promise<ApiResponse<LoginResponse>> => {
    try {
      const response = await api.post<ApiResponse<LoginResponse>>("/api/auth/login", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  logout: async (): Promise<ApiResponse<null>> => {
    try {
      const response = await api.post<ApiResponse<null>>("/api/auth/logout");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
