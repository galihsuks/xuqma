import { zodResolver } from "@hookform/resolvers/zod";
import {
  House,
  LogOut,
  MapPinned,
  Pencil,
  Plus,
  Save,
  ShieldCheck,
  Star,
  Trash2,
  UserRound,
} from "lucide-react";
import { useMemo, useState } from "react";
import { useForm } from "react-hook-form";
import { useNavigate } from "react-router-dom";
import { useAuthLogoutMutation } from "../../../api/auth/authQuery";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Badge, Button, FormInput } from "../../../components/ui";
import { usePageTitle } from "../../../hooks/usePageTitle";
import { queryClient } from "../../../lib/queryClient";
import { useAuthActions, useUser } from "../../../store/authStore";
import {
  useCustomerDefaultAddress,
  useCustomerProfileActions,
  useCustomerPreferredCourierService,
  useCustomerPreferredPaymentMethod,
  useCustomerPreferredPhone,
  useCustomerSavedAddresses,
  type CustomerSavedAddress,
} from "../../../store/customerProfileStore";
import { useNotificationStore } from "../../../store/notifStore";
import { CustomerAddressModal } from "./components/CustomerAddressModal";
import {
  customerProfileSchema,
  type CustomerProfileSchemaType,
} from "./schema/CustomerProfileSchema";
import type { CustomerAddressSchemaType } from "./schema/CustomerAddressSchema";

const paymentMethodOptions = [
  { value: "Bank Transfer", label: "Bank Transfer" },
  { value: "Virtual Account", label: "Virtual Account" },
  { value: "E-Wallet", label: "E-Wallet" },
];

const courierOptions = [
  { value: "Instant Courier", label: "Instant Courier" },
  { value: "Regular Delivery", label: "Regular Delivery" },
  { value: "Cargo Service", label: "Cargo Service" },
];

export const CustomerProfilePage = () => {
  usePageTitle("Profile");

  const navigate = useNavigate();
  const user = useUser();
  const { logout } = useAuthActions();
  const { addToast } = useNotificationStore();
  const { mutate: logoutMutation, isPending: isLogoutPending } = useAuthLogoutMutation();
  const addresses = useCustomerSavedAddresses();
  const defaultAddress = useCustomerDefaultAddress();
  const preferredPhone = useCustomerPreferredPhone();
  const preferredPaymentMethod = useCustomerPreferredPaymentMethod();
  const preferredCourierService = useCustomerPreferredCourierService();
  const { saveProfilePreferences, removeAddress, setDefaultAddress, upsertAddress } =
    useCustomerProfileActions();
  const [modalMode, setModalMode] = useState<"create" | "edit" | null>(null);
  const [editingAddress, setEditingAddress] = useState<CustomerSavedAddress | null>(null);

  const { control, handleSubmit } = useForm<CustomerProfileSchemaType>({
    resolver: zodResolver(customerProfileSchema),
    defaultValues: {
      preferred_phone: preferredPhone,
      preferred_payment_method: preferredPaymentMethod,
      preferred_courier_service: preferredCourierService,
    },
    values: {
      preferred_phone: preferredPhone,
      preferred_payment_method: preferredPaymentMethod,
      preferred_courier_service: preferredCourierService,
    },
  });

  const addressCountLabel = useMemo(
    () => `${addresses.length} saved address(es)`,
    [addresses.length],
  );

  const onSubmitPreferences = (values: CustomerProfileSchemaType) => {
    saveProfilePreferences(values);
    addToast("Customer profile preferences saved.", "success");
  };

  const onLogout = () => {
    logoutMutation(undefined, {
      onSuccess: (response) => {
        if (response.message) {
          addToast(response.message, "success");
        }
      },
      onError: (error) => {
        addToast(error.message, "error");
      },
      onSettled: () => {
        queryClient.clear();
        logout();
        navigate("/login");
      },
    });
  };

  const onSubmitAddress = (values: CustomerAddressSchemaType) => {
    upsertAddress({
      id: editingAddress?.id,
      label: values.label,
      recipient_name: values.recipient_name,
      phone: values.phone,
      address: values.address,
      is_default: values.is_default === "1",
    });

    addToast(modalMode === "create" ? "Saved address added." : "Saved address updated.", "success");
    setEditingAddress(null);
    setModalMode(null);
  };

  return (
    <>
      <PageHeader
        title="Customer Profile"
        subtitle="Save delivery preferences and address book entries so the checkout flow can fill itself faster."
        breadcrumbs={[
          { label: "Customer", route: undefined },
          { label: "Profile", route: undefined },
        ]}
      />

      <div className="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section className="space-y-6">
          <div className="rounded-3xl border border-primary-100 bg-gradient-to-br from-primary-50 via-white to-secondary-50 p-6">
            <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
              <div className="flex items-start gap-3">
                <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-600 text-white">
                  <UserRound className="h-5 w-5" />
                </div>
                <div>
                  <p className="text-sm uppercase tracking-[0.18em] text-primary-700">
                    Account snapshot
                  </p>
                  <h2 className="mt-2 text-2xl font-semibold text-dark-900">
                    {user?.full_name ?? "Customer"}
                  </h2>
                  <p className="mt-1 text-sm text-dark-500">{user?.email ?? "-"}</p>
                  <p className="mt-1 text-sm text-dark-500">Username: {user?.username ?? "-"}</p>
                </div>
              </div>

              <Button
                type="button"
                variant="danger-outline"
                icon={LogOut}
                loading={isLogoutPending}
                onClick={onLogout}
              >
                {isLogoutPending ? "Signing out..." : "Sign Out"}
              </Button>
            </div>
          </div>

          <div className="rounded-3xl border border-secondary-100 bg-white p-6">
            <div className="flex items-start gap-3">
              <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600">
                <ShieldCheck className="h-5 w-5" />
              </div>
              <div>
                <h2 className="text-lg font-semibold text-dark-900">Checkout preferences</h2>
                <p className="mt-1 text-sm text-dark-500">
                  These values are used as your default contact and delivery preference inside the
                  cart page.
                </p>
              </div>
            </div>

            <form
              className="mt-5 grid gap-4 md:grid-cols-2"
              onSubmit={handleSubmit(onSubmitPreferences)}
            >
              <FormInput
                control={control}
                name="preferred_phone"
                label="Preferred Phone"
                placeholder="0812xxxxxxx"
              />
              <FormInput
                control={control}
                name="preferred_courier_service"
                type="dropdown"
                label="Preferred Courier"
                placeholder="Choose courier"
                dropdownOptions={courierOptions}
              />
              <FormInput
                control={control}
                name="preferred_payment_method"
                type="dropdown"
                label="Preferred Payment"
                placeholder="Choose payment method"
                dropdownOptions={paymentMethodOptions}
              />
              <div className="hidden md:block" />
              <div className="md:col-span-2">
                <Button type="button" icon={Save} onClick={handleSubmit(onSubmitPreferences)}>
                  Save Preferences
                </Button>
              </div>
            </form>
          </div>
        </section>

        <section className="space-y-6">
          <div className="flex flex-col gap-4 rounded-3xl border border-primary-100 bg-white p-6 md:flex-row md:items-start md:justify-between">
            <div>
              <div className="flex items-center gap-3">
                <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-primary-50 text-primary-600">
                  <MapPinned className="h-5 w-5" />
                </div>
                <div>
                  <h2 className="text-lg font-semibold text-dark-900">Address Book</h2>
                  <p className="mt-1 text-sm text-dark-500">{addressCountLabel}</p>
                </div>
              </div>
              {defaultAddress ? (
                <p className="mt-4 text-sm text-dark-500">
                  Default address:{" "}
                  <span className="font-semibold text-dark-900">{defaultAddress.label}</span>
                </p>
              ) : (
                <p className="mt-4 text-sm text-dark-500">
                  No saved address yet. Add one so checkout can fill itself automatically.
                </p>
              )}
            </div>

            <Button
              type="button"
              icon={Plus}
              onClick={() => {
                setEditingAddress(null);
                setModalMode("create");
              }}
            >
              Add Address
            </Button>
          </div>

          {addresses.length === 0 ? (
            <article className="rounded-3xl border border-dark-200 bg-white p-6 text-center">
              <House className="mx-auto h-8 w-8 text-primary-600" />
              <h2 className="mt-4 text-xl font-semibold text-dark-900">No saved address yet</h2>
              <p className="mt-2 text-sm text-dark-500">
                Create your first delivery address to speed up future checkouts.
              </p>
            </article>
          ) : (
            <div className="grid gap-4">
              {addresses.map((address) => (
                <article
                  key={address.id}
                  className="rounded-3xl border border-dark-200 bg-white p-5"
                >
                  <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                    <div>
                      <div className="flex flex-wrap items-center gap-2">
                        <h3 className="text-lg font-semibold text-dark-900">{address.label}</h3>
                        {address.is_default ? (
                          <Badge variant="success-outline">
                            <Star className="mr-1 h-3.5 w-3.5" />
                            Default
                          </Badge>
                        ) : null}
                      </div>
                      <p className="mt-2 text-sm font-medium text-dark-700">
                        {address.recipient_name}
                      </p>
                      <p className="mt-1 text-sm text-dark-500">{address.phone}</p>
                      <p className="mt-3 text-sm leading-7 text-dark-600">{address.address}</p>
                    </div>

                    <div className="flex flex-wrap gap-2">
                      {!address.is_default ? (
                        <Button
                          type="button"
                          variant="success-outline"
                          onClick={() => {
                            setDefaultAddress(address.id);
                            addToast("Default address updated.", "success");
                          }}
                        >
                          Set Default
                        </Button>
                      ) : null}
                      <Button
                        type="button"
                        variant="warning"
                        icon={Pencil}
                        onClick={() => {
                          setEditingAddress(address);
                          setModalMode("edit");
                        }}
                      ></Button>
                      <Button
                        type="button"
                        variant="danger-outline"
                        icon={Trash2}
                        onClick={() => {
                          removeAddress(address.id);
                          addToast("Saved address removed.", "success");
                        }}
                      ></Button>
                    </div>
                  </div>
                </article>
              ))}
            </div>
          )}
        </section>
      </div>

      <CustomerAddressModal
        open={modalMode !== null}
        mode={modalMode ?? "create"}
        initialValue={editingAddress}
        onClose={() => {
          setEditingAddress(null);
          setModalMode(null);
        }}
        onSubmitAddress={onSubmitAddress}
      />
    </>
  );
};
