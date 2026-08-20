import type { ApiResponse } from "../../interfaces/api";
import type { Cart, CartCount, CartItemPayload, CartUpdatePayload } from "../../interfaces/cart";
import type { ID } from "../../interfaces/common";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const cartApi = {
  current: async (): Promise<ApiResponse<Cart>> => {
    try {
      const response = await api.get<ApiResponse<Cart>>("/api/cart/");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  count: async (): Promise<ApiResponse<CartCount>> => {
    try {
      const response = await api.get<ApiResponse<CartCount>>("/api/cart/count");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  addItem: async (payload: CartItemPayload): Promise<ApiResponse<Cart>> => {
    try {
      const response = await api.post<ApiResponse<Cart>>("/api/cart/items", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  updateItem: async (productId: ID, payload: CartUpdatePayload): Promise<ApiResponse<Cart>> => {
    try {
      const response = await api.put<ApiResponse<Cart>>(`/api/cart/items/${productId}`, payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  removeItem: async (productId: ID): Promise<ApiResponse<Cart>> => {
    try {
      const response = await api.delete<ApiResponse<Cart>>(`/api/cart/items/${productId}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  clear: async (): Promise<ApiResponse<Cart>> => {
    try {
      const response = await api.delete<ApiResponse<Cart>>("/api/cart/clear");
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
