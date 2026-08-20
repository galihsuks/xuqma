import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import type { CartItemPayload, CartUpdatePayload } from "../../interfaces/cart";
import { queryKeys } from "../queryKeys";
import { cartApi } from "./cartApi";

const useInvalidateCartQueries = () => {
  const queryClient = useQueryClient();

  return async () => {
    await Promise.all([
      queryClient.invalidateQueries({ queryKey: queryKeys.cart.current }),
      queryClient.invalidateQueries({ queryKey: queryKeys.cart.count }),
    ]);
  };
};

export const useCartQuery = () => {
  return useQuery({
    queryKey: queryKeys.cart.current,
    queryFn: cartApi.current,
    staleTime: 1000 * 60 * 5,
    gcTime: 1000 * 60 * 10,
    refetchOnMount: false,
    refetchOnReconnect: false,
    refetchOnWindowFocus: false,
  });
};

export const useCartCountQuery = () => {
  return useQuery({
    queryKey: queryKeys.cart.count,
    queryFn: cartApi.count,
    staleTime: 1000 * 60 * 5,
    gcTime: 1000 * 60 * 10,
    refetchOnMount: false,
    refetchOnReconnect: false,
    refetchOnWindowFocus: false,
  });
};

export const useAddCartItemMutation = () => {
  const invalidateCartQueries = useInvalidateCartQueries();

  return useMutation({
    mutationFn: (payload: CartItemPayload) => cartApi.addItem(payload),
    onSuccess: async () => {
      await invalidateCartQueries();
    },
  });
};

export const useUpdateCartItemMutation = () => {
  const invalidateCartQueries = useInvalidateCartQueries();

  return useMutation({
    mutationFn: ({ productId, payload }: { productId: string; payload: CartUpdatePayload }) =>
      cartApi.updateItem(productId, payload),
    onSuccess: async () => {
      await invalidateCartQueries();
    },
  });
};

export const useRemoveCartItemMutation = () => {
  const invalidateCartQueries = useInvalidateCartQueries();

  return useMutation({
    mutationFn: (productId: string) => cartApi.removeItem(productId),
    onSuccess: async () => {
      await invalidateCartQueries();
    },
  });
};

export const useClearCartMutation = () => {
  const invalidateCartQueries = useInvalidateCartQueries();

  return useMutation({
    mutationFn: cartApi.clear,
    onSuccess: async () => {
      await invalidateCartQueries();
    },
  });
};
