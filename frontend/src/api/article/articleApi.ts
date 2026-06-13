import type { ApiResponse } from "../../interfaces/api";
import type { ID } from "../../interfaces/common";
import type { Article, ArticleListQuery, ArticlePayload } from "../../interfaces/article";
import api from "../axios";
import { getApiErrorMessage } from "../apiError";

export const articleApi = {
  index: async (query?: ArticleListQuery): Promise<ApiResponse<Article[]>> => {
    try {
      const response = await api.get<ApiResponse<Article[]>>("/api/article/", { params: query });
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  detail: async (id: ID): Promise<ApiResponse<Article>> => {
    try {
      const response = await api.get<ApiResponse<Article>>(`/api/article/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  create: async (payload: ArticlePayload): Promise<ApiResponse<Article>> => {
    try {
      const response = await api.post<ApiResponse<Article>>("/api/article/", payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  update: async (id: ID, payload: ArticlePayload): Promise<ApiResponse<Article>> => {
    try {
      const response = await api.put<ApiResponse<Article>>(`/api/article/${id}`, payload);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
  delete: async (id: ID): Promise<ApiResponse<Article>> => {
    try {
      const response = await api.delete<ApiResponse<Article>>(`/api/article/${id}`);
      return response.data;
    } catch (error) {
      throw new Error(getApiErrorMessage(error));
    }
  },
};
