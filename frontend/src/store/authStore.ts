import { create } from "zustand";
import { persist } from "zustand/middleware";
import type { User } from "../interfaces/auth";

interface AuthState {
  user: User | null;
  token: string | null;
  isAuthenticated: boolean;
  actions: {
    login: (user: User, token: string) => void;
    logout: () => void;
  };
}

export const useAuthStore = create<AuthState>()(
  persist(
    (set) => ({
      user: null,
      token: null,
      isAuthenticated: false,
      actions: {
        login: (user, token) => set({ user, token, isAuthenticated: true }),
        logout: () => {
          set({ user: null, token: null, isAuthenticated: false });
          localStorage.removeItem("auth-storage");
        },
      },
    }),
    {
      name: "auth-storage",
      partialize: (state) => ({
        user: state.user,
        token: state.token,
        isAuthenticated: state.isAuthenticated,
      }),
    },
  ),
);

export const useAuthActions = () => useAuthStore((state) => state.actions);
export const useUser = () => useAuthStore((state) => state.user);
