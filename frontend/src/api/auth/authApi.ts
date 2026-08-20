import type { ApiResponse } from "../../interfaces/api";
import type { User } from "../../interfaces/auth";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const authApi = {
  logout: async (): Promise<ApiResponse<null>> => {
    try {
      const response = await api.post<ApiResponse<null>>("/api/auth/logout");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  me: async (): Promise<ApiResponse<User>> => {
    try {
      const response = await api.get<ApiResponse<User>>("/api/auth/me");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
