import type { ApiResponse } from "../../interfaces/api";
import type { ID } from "../../interfaces/common";
import type { Order, OrderCreatePayload, OrderListQuery, OrderUpdatePayload } from "../../interfaces/order";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const orderApi = {
  index: async (query?: OrderListQuery): Promise<ApiResponse<Order[]>> => {
    try {
      const response = await api.get<ApiResponse<Order[]>>("/api/order/", { params: query });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  detail: async (id: ID): Promise<ApiResponse<Order>> => {
    try {
      const response = await api.get<ApiResponse<Order>>(`/api/order/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  create: async (payload: OrderCreatePayload): Promise<ApiResponse<Order>> => {
    try {
      const response = await api.post<ApiResponse<Order>>("/api/order/", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  update: async (id: ID, payload: OrderUpdatePayload): Promise<ApiResponse<Order>> => {
    try {
      const response = await api.put<ApiResponse<Order>>(`/api/order/${id}`, payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
