import type { ApiResponse } from "../../interfaces/api";
import type { DropdownOption } from "../../interfaces/dropdown";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const dropdownApi = {
  role: async (keywords = ""): Promise<ApiResponse<DropdownOption[]>> => {
    try {
      const response = await api.get<ApiResponse<DropdownOption[]>>("/api/dropdown/role", {
        params: { keywords },
      });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  user: async (keywords = ""): Promise<ApiResponse<DropdownOption[]>> => {
    try {
      const response = await api.get<ApiResponse<DropdownOption[]>>("/api/dropdown/user", {
        params: { keywords },
      });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
