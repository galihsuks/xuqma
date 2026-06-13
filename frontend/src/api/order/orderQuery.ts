import { useMutation, useQuery } from "@tanstack/react-query";
import type { OrderCreatePayload, OrderListQuery, OrderUpdatePayload } from "../../interfaces/order";
import { queryKeys } from "../queryKeys";
import { orderApi } from "./orderApi";

export const useOrderListQuery = (params?: OrderListQuery) => {
  return useQuery({
    queryKey: queryKeys.order.list(params),
    queryFn: () => orderApi.index(params),
  });
};

export const useOrderDetailQuery = (id: string) => {
  return useQuery({
    queryKey: queryKeys.order.detail(id),
    queryFn: () => orderApi.detail(id),
    enabled: Boolean(id),
  });
};

export const useCreateOrderMutation = () => {
  return useMutation({
    mutationFn: (payload: OrderCreatePayload) => orderApi.create(payload),
  });
};

export const useUpdateOrderMutation = () => {
  return useMutation({
    mutationFn: ({ id, payload }: { id: string; payload: OrderUpdatePayload }) =>
      orderApi.update(id, payload),
  });
};
