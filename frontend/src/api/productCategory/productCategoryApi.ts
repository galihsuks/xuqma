import type { ApiResponse } from "../../interfaces/api";
import type { ID } from "../../interfaces/common";
import type { ProductCategory, ProductCategoryListQuery, ProductCategoryPayload } from "../../interfaces/productCategory";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const productCategoryApi = {
  index: async (query?: ProductCategoryListQuery): Promise<ApiResponse<ProductCategory[]>> => {
    try {
      const response = await api.get<ApiResponse<ProductCategory[]>>("/api/product-category/", {
        params: query,
      });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  detail: async (id: ID): Promise<ApiResponse<ProductCategory>> => {
    try {
      const response = await api.get<ApiResponse<ProductCategory>>(`/api/product-category/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  create: async (payload: ProductCategoryPayload): Promise<ApiResponse<ProductCategory>> => {
    try {
      const response = await api.post<ApiResponse<ProductCategory>>("/api/product-category/", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  update: async (id: ID, payload: ProductCategoryPayload): Promise<ApiResponse<ProductCategory>> => {
    try {
      const response = await api.put<ApiResponse<ProductCategory>>(`/api/product-category/${id}`, payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  delete: async (id: ID): Promise<ApiResponse<ProductCategory>> => {
    try {
      const response = await api.delete<ApiResponse<ProductCategory>>(`/api/product-category/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
