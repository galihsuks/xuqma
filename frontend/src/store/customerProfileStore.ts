import { create } from "zustand";
import { persist } from "zustand/middleware";

export interface CustomerSavedAddress {
  id: string;
  label: string;
  recipient_name: string;
  phone: string;
  address: string;
  is_default: boolean;
}

interface CustomerProfileState {
  preferred_phone: string;
  preferred_payment_method: "Bank Transfer" | "Virtual Account" | "E-Wallet";
  preferred_courier_service: "Instant Courier" | "Regular Delivery" | "Cargo Service";
  addresses: CustomerSavedAddress[];
  actions: {
    saveProfilePreferences: (payload: {
      preferred_phone: string;
      preferred_payment_method: "Bank Transfer" | "Virtual Account" | "E-Wallet";
      preferred_courier_service: "Instant Courier" | "Regular Delivery" | "Cargo Service";
    }) => void;
    upsertAddress: (payload: Omit<CustomerSavedAddress, "id"> & { id?: string }) => void;
    removeAddress: (id: string) => void;
    setDefaultAddress: (id: string) => void;
  };
}

const ensureSingleDefault = (addresses: CustomerSavedAddress[]) => {
  const hasDefault = addresses.some((item) => item.is_default);
  if (hasDefault) {
    return addresses;
  }

  return addresses.map((item, index) => ({
    ...item,
    is_default: index === 0,
  }));
};

export const useCustomerProfileStore = create<CustomerProfileState>()(
  persist(
    (set) => ({
      preferred_phone: "",
      preferred_payment_method: "Virtual Account",
      preferred_courier_service: "Regular Delivery",
      addresses: [],
      actions: {
        saveProfilePreferences: (payload) =>
          set({
            preferred_phone: payload.preferred_phone,
            preferred_payment_method: payload.preferred_payment_method,
            preferred_courier_service: payload.preferred_courier_service,
          }),
        upsertAddress: (payload) =>
          set((state) => {
            const nextId = payload.id ?? `addr-${Date.now()}`;
            const normalized = {
              id: nextId,
              label: payload.label,
              recipient_name: payload.recipient_name,
              phone: payload.phone,
              address: payload.address,
              is_default: payload.is_default,
            };

            const nextAddresses = state.addresses.some((item) => item.id === nextId)
              ? state.addresses.map((item) =>
                  item.id === nextId
                    ? normalized
                    : payload.is_default
                      ? { ...item, is_default: false }
                      : item,
                )
              : [
                  ...state.addresses.map((item) =>
                    payload.is_default ? { ...item, is_default: false } : item,
                  ),
                  normalized,
                ];

            return {
              addresses: ensureSingleDefault(nextAddresses),
            };
          }),
        removeAddress: (id) =>
          set((state) => ({
            addresses: ensureSingleDefault(state.addresses.filter((item) => item.id !== id)),
          })),
        setDefaultAddress: (id) =>
          set((state) => ({
            addresses: state.addresses.map((item) => ({
              ...item,
              is_default: item.id === id,
            })),
          })),
      },
    }),
    {
      name: "customer-profile-storage",
      partialize: (state) => ({
        preferred_phone: state.preferred_phone,
        preferred_payment_method: state.preferred_payment_method,
        preferred_courier_service: state.preferred_courier_service,
        addresses: state.addresses,
      }),
    },
  ),
);

export const useCustomerPreferredPhone = () =>
  useCustomerProfileStore((state) => state.preferred_phone);

export const useCustomerPreferredPaymentMethod = () =>
  useCustomerProfileStore((state) => state.preferred_payment_method);

export const useCustomerPreferredCourierService = () =>
  useCustomerProfileStore((state) => state.preferred_courier_service);

export const useCustomerSavedAddresses = () =>
  useCustomerProfileStore((state) => state.addresses);

export const useCustomerDefaultAddress = () =>
  useCustomerProfileStore((state) => state.addresses.find((item) => item.is_default) ?? null);

export const useCustomerProfileActions = () =>
  useCustomerProfileStore((state) => state.actions);
