import type { ApiResponse } from "../../interfaces/api";
import type { ID } from "../../interfaces/common";
import type { Product, ProductListQuery, ProductPayload } from "../../interfaces/product";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const productApi = {
  index: async (query?: ProductListQuery): Promise<ApiResponse<Product[]>> => {
    try {
      const response = await api.get<ApiResponse<Product[]>>("/api/product/", { params: query });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  detail: async (id: ID): Promise<ApiResponse<Product>> => {
    try {
      const response = await api.get<ApiResponse<Product>>(`/api/product/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  create: async (payload: ProductPayload): Promise<ApiResponse<Product>> => {
    try {
      const response = await api.post<ApiResponse<Product>>("/api/product/", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  update: async (id: ID, payload: ProductPayload): Promise<ApiResponse<Product>> => {
    try {
      const response = await api.put<ApiResponse<Product>>(`/api/product/${id}`, payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  delete: async (id: ID): Promise<ApiResponse<Product>> => {
    try {
      const response = await api.delete<ApiResponse<Product>>(`/api/product/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
