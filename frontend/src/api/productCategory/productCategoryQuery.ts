import { useMutation, useQuery } from "@tanstack/react-query";
import type { ProductCategoryListQuery, ProductCategoryPayload } from "../../interfaces/productCategory";
import { queryKeys } from "../queryKeys";
import { productCategoryApi } from "./productCategoryApi";

export const useProductCategoryListQuery = (params?: ProductCategoryListQuery) => {
  return useQuery({
    queryKey: queryKeys.productCategory.list(params),
    queryFn: () => productCategoryApi.index(params),
  });
};

export const useProductCategoryDetailQuery = (id: string) => {
  return useQuery({
    queryKey: queryKeys.productCategory.detail(id),
    queryFn: () => productCategoryApi.detail(id),
    enabled: Boolean(id),
  });
};

export const useCreateProductCategoryMutation = () => {
  return useMutation({
    mutationFn: (payload: ProductCategoryPayload) => productCategoryApi.create(payload),
  });
};

export const useUpdateProductCategoryMutation = () => {
  return useMutation({
    mutationFn: ({ id, payload }: { id: string; payload: ProductCategoryPayload }) =>
      productCategoryApi.update(id, payload),
  });
};

export const useDeleteProductCategoryMutation = () => {
  return useMutation({
    mutationFn: (id: string) => productCategoryApi.delete(id),
  });
};
