import { create } from "zustand";
import { persist } from "zustand/middleware";

export interface CustomerCartItem {
  product_id: string;
  product_name: string;
  category_name: string;
  price: number;
  qty: number;
}

interface CustomerCartState {
  items: CustomerCartItem[];
  actions: {
    addItem: (item: Omit<CustomerCartItem, "qty">, qty?: number) => void;
    updateQty: (productId: string, qty: number) => void;
    removeItem: (productId: string) => void;
    clearCart: () => void;
  };
}

export const useCustomerCartStore = create<CustomerCartState>()(
  persist(
    (set) => ({
      items: [],
      actions: {
        addItem: (item, qty = 1) =>
          set((state) => {
            const existing = state.items.find((cartItem) => cartItem.product_id === item.product_id);
            if (existing) {
              return {
                items: state.items.map((cartItem) =>
                  cartItem.product_id === item.product_id
                    ? { ...cartItem, qty: cartItem.qty + qty }
                    : cartItem,
                ),
              };
            }

            return {
              items: [...state.items, { ...item, qty }],
            };
          }),
        updateQty: (productId, qty) =>
          set((state) => ({
            items: state.items
              .map((item) => (item.product_id === productId ? { ...item, qty: Math.max(1, qty) } : item))
              .filter((item) => item.qty > 0),
          })),
        removeItem: (productId) =>
          set((state) => ({
            items: state.items.filter((item) => item.product_id !== productId),
          })),
        clearCart: () => set({ items: [] }),
      },
    }),
    {
      name: "customer-cart-storage",
      partialize: (state) => ({
        items: state.items,
      }),
    },
  ),
);

export const useCustomerCartItems = () => useCustomerCartStore((state) => state.items);
export const useCustomerCartActions = () => useCustomerCartStore((state) => state.actions);
