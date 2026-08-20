import { useMutation, useQuery } from "@tanstack/react-query";
import { queryKeys } from "../queryKeys";
import { authApi } from "./authApi";

export const useAuthLogoutMutation = () => {
  return useMutation({
    mutationFn: authApi.logout,
  });
};

export const useAuthMeQuery = () => {
  return useQuery({
    queryKey: queryKeys.auth.me,
    queryFn: authApi.me,
    staleTime: 1000 * 60 * 5,
    gcTime: 1000 * 60 * 10,
    retry: false,
    refetchOnMount: false,
    refetchOnReconnect: false,
    refetchOnWindowFocus: false,
  });
};
