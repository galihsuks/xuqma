import { useMutation, useQuery } from "@tanstack/react-query";
import type { ArticleListQuery, ArticlePayload } from "../../interfaces/article";
import { queryKeys } from "../queryKeys";
import { articleApi } from "./articleApi";

export const useArticleListQuery = (params?: ArticleListQuery) => {
  return useQuery({
    queryKey: queryKeys.article.list(params),
    queryFn: () => articleApi.index(params),
  });
};

export const useArticleDetailQuery = (id: string) => {
  return useQuery({
    queryKey: queryKeys.article.detail(id),
    queryFn: () => articleApi.detail(id),
    enabled: Boolean(id),
  });
};

export const useCreateArticleMutation = () => {
  return useMutation({
    mutationFn: (payload: ArticlePayload) => articleApi.create(payload),
  });
};

export const useUpdateArticleMutation = () => {
  return useMutation({
    mutationFn: ({ id, payload }: { id: string; payload: ArticlePayload }) =>
      articleApi.update(id, payload),
  });
};

export const useDeleteArticleMutation = () => {
  return useMutation({
    mutationFn: (id: string) => articleApi.delete(id),
  });
};
