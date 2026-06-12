import { useMutation } from "@tanstack/react-query";
import { authApi } from "./authApi";

export const useAuthLoginMutation = () => {
  return useMutation({
    mutationFn: authApi.login,
  });
};

export const useAuthLogoutMutation = () => {
  return useMutation({
    mutationFn: authApi.logout,
  });
};
