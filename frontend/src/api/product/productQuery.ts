import { useMutation, useQuery } from "@tanstack/react-query";
import type { ProductListQuery, ProductPayload } from "../../interfaces/product";
import { queryKeys } from "../queryKeys";
import { productApi } from "./productApi";

export const useProductListQuery = (params?: ProductListQuery) => {
  return useQuery({
    queryKey: queryKeys.product.list(params),
    queryFn: () => productApi.index(params),
  });
};

export const useProductDetailQuery = (id: string) => {
  return useQuery({
    queryKey: queryKeys.product.detail(id),
    queryFn: () => productApi.detail(id),
    enabled: Boolean(id),
  });
};

export const useCreateProductMutation = () => {
  return useMutation({
    mutationFn: (payload: ProductPayload) => productApi.create(payload),
  });
};

export const useUpdateProductMutation = () => {
  return useMutation({
    mutationFn: ({ id, payload }: { id: string; payload: ProductPayload }) =>
      productApi.update(id, payload),
  });
};

export const useDeleteProductMutation = () => {
  return useMutation({
    mutationFn: (id: string) => productApi.delete(id),
  });
};
