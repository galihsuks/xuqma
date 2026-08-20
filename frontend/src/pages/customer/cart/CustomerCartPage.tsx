import { zodResolver } from "@hookform/resolvers/zod";
import {
  CreditCard,
  MapPinned,
  Minus,
  Plus,
  ShieldCheck,
  ShoppingCart,
  Trash2,
  Truck,
  WalletCards,
} from "lucide-react";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useQueryClient } from "@tanstack/react-query";
import { useNavigate } from "react-router-dom";
import {
  useCartQuery,
  useClearCartMutation,
  useRemoveCartItemMutation,
  useUpdateCartItemMutation,
} from "../../../api/cart/cartQuery";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Badge, Button, FormInput } from "../../../components/ui";
import { useCreateOrderMutation } from "../../../api/order/orderQuery";
import { usePageTitle } from "../../../hooks/usePageTitle";
import { queryKeys } from "../../../api/queryKeys";
import { useApiFormError } from "../../../hooks/useApiFormError";
import { useUser } from "../../../store/authStore";
import {
  useCustomerDefaultAddress,
  useCustomerPreferredCourierService,
  useCustomerPreferredPaymentMethod,
  useCustomerPreferredPhone,
} from "../../../store/customerProfileStore";
import { useNotificationStore } from "../../../store/notifStore";
import {
  customerCheckoutSchema,
  type CustomerCheckoutSchemaType,
} from "./schema/CustomerCheckoutSchema";

const courierOptions = [
  { value: "Instant Courier", label: "Instant Courier" },
  { value: "Regular Delivery", label: "Regular Delivery" },
  { value: "Cargo Service", label: "Cargo Service" },
];

const paymentMethodOptions = [
  { value: "Bank Transfer", label: "Bank Transfer" },
  { value: "Virtual Account", label: "Virtual Account" },
  { value: "E-Wallet", label: "E-Wallet" },
];

const formatCurrency = (value: number) => {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(value);
};

export const CustomerCartPage = () => {
  usePageTitle("Cart");

  const user = useUser();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const { addToast } = useNotificationStore();
  const { handleApiFormError } = useApiFormError({ logEvent: "customer_checkout_failed" });
  const defaultAddress = useCustomerDefaultAddress();
  const preferredPhone = useCustomerPreferredPhone();
  const preferredCourierService = useCustomerPreferredCourierService();
  const preferredPaymentMethod = useCustomerPreferredPaymentMethod();
  const { data: cartData } = useCartQuery();
  const { mutate: createOrderMutation, isPending: isCheckoutPending } = useCreateOrderMutation();
  const { mutate: updateCartItemMutation } = useUpdateCartItemMutation();
  const { mutate: removeCartItemMutation } = useRemoveCartItemMutation();
  const { mutate: clearCartMutation } = useClearCartMutation();
  const cart = cartData?.data;
  const cartItems = cart?.items ?? [];
  const { control, getValues, handleSubmit, reset, setValue } = useForm<CustomerCheckoutSchemaType>(
    {
      resolver: zodResolver(customerCheckoutSchema),
      defaultValues: {
        customer_phone: preferredPhone || defaultAddress?.phone || "",
        shipping_address: defaultAddress?.address || "",
        courier_service: preferredCourierService,
        payment_method: preferredPaymentMethod,
        order_note: "",
      },
    },
  );

  useEffect(() => {
    if (getValues("customer_phone").trim() === "" && preferredPhone !== "") {
      setValue("customer_phone", preferredPhone);
    }

    if (getValues("shipping_address").trim() === "" && defaultAddress?.address) {
      setValue("shipping_address", defaultAddress.address);
    }

    if (getValues("courier_service").trim() === "") {
      setValue("courier_service", preferredCourierService);
    }

    if (getValues("payment_method").trim() === "") {
      setValue("payment_method", preferredPaymentMethod);
    }
  }, [
    defaultAddress?.address,
    getValues,
    preferredCourierService,
    preferredPaymentMethod,
    preferredPhone,
    setValue,
  ]);

  const itemSubtotal = cartItems.reduce(
    (total, item) => total + Number(item.price) * Number(item.qty),
    0,
  );
  const shippingProtection = cartItems.length > 0 ? 25000 : 0;
  const estimatedShipping = cartItems.length > 0 ? 40000 : 0;
  const grandTotal = itemSubtotal + shippingProtection + estimatedShipping;

  const onCheckout = (values: CustomerCheckoutSchemaType) => {
    if (!user || cartItems.length === 0) {
      return;
    }

    const noteLines = [
      "Checkout Metadata",
      `Payment Method: ${values.payment_method}`,
      `Courier Service: ${values.courier_service}`,
      `Shipping Address: ${values.shipping_address}`,
    ];

    const orderNote = values.order_note ?? "";
    if (orderNote.trim() !== "") {
      noteLines.push(`Customer Note: ${orderNote.trim()}`);
    }

    createOrderMutation(
      {
        user_id: user.id,
        customer_name: user.full_name,
        customer_email: user.email,
        customer_phone: values.customer_phone,
        channel: "Website",
        status: "Waiting Payment",
        payment_status: "Unpaid",
        notes: noteLines.join("\n"),
        items: cartItems.map((item) => ({
          product_id: item.product_id,
          product_name: item.product_name ?? "",
          qty: item.qty,
          unit_price: item.price,
        })),
      },
      {
        onSuccess: (response) => {
          addToast(response.message, "success");
          clearCartMutation();
          void queryClient.invalidateQueries({ queryKey: ["order"] });
          void queryClient.invalidateQueries({ queryKey: queryKeys.order.list({}) });
          void queryClient.invalidateQueries({ queryKey: queryKeys.cart.current });
          void queryClient.invalidateQueries({ queryKey: queryKeys.cart.count });
          reset();
          if (response.data?.id) {
            navigate(`/customer/orders/${response.data.id}`);
          }
        },
        onError: (error) => {
          handleApiFormError(error, {
            customer_id: user.id,
            cart_items: cartItems.length,
            payment_method: values.payment_method,
            courier_service: values.courier_service,
          });
        },
      },
    );
  };

  return (
    <>
      <PageHeader
        title="Shopping Cart"
        subtitle="Review the items you plan to order. This customer flow now uses backend catalog data and can create a real order record."
        breadcrumbs={[
          { label: "Customer", route: undefined },
          { label: "Cart", route: undefined },
        ]}
      />

      <div className="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
        <section className="space-y-4">
          {cartItems.length === 0 ? (
            <article className="rounded-3xl border border-primary-100 bg-white p-6 text-center">
              <ShoppingCart className="mx-auto h-8 w-8 text-primary-600" />
              <h2 className="mt-4 text-xl font-semibold text-dark-900">Your cart is empty</h2>
              <p className="mt-2 text-sm text-dark-500">
                Add products from the storefront to start your order.
              </p>
            </article>
          ) : (
            cartItems.map((item) => (
              <article
                key={item.product_id}
                className="rounded-3xl border border-primary-100 bg-gradient-to-br from-white via-white to-primary-50/70 p-5"
              >
                <div className="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                  <div>
                    <Badge variant="secondary-outline">{item.category_name}</Badge>
                    <h2 className="mt-3 text-xl font-semibold text-dark-900">
                      {item.product_name}
                    </h2>
                    <p className="mt-2 text-sm text-dark-500">
                      Unit price: {formatCurrency(Number(item.price))}
                    </p>
                  </div>
                  <div className="text-left md:text-right">
                    <p className="text-sm text-dark-500">Subtotal</p>
                    <p className="text-xl font-semibold text-primary-700">
                      {formatCurrency(Number(item.price) * Number(item.qty))}
                    </p>
                  </div>
                </div>

                <div className="mt-5 flex flex-wrap items-center justify-between gap-3 border-t border-primary-100 pt-4">
                  <div className="flex items-center gap-2">
                    <Button
                      type="button"
                      variant="light-outline"
                      icon={Minus}
                      onClick={() =>
                        updateCartItemMutation({
                          productId: item.product_id,
                          payload: { qty: Math.max(1, Number(item.qty) - 1) },
                        })
                      }
                    />
                    <span className="min-w-10 text-center text-sm font-semibold text-dark-900">
                      {item.qty}
                    </span>
                    <Button
                      type="button"
                      variant="light-outline"
                      icon={Plus}
                      onClick={() =>
                        updateCartItemMutation({
                          productId: item.product_id,
                          payload: { qty: Number(item.qty) + 1 },
                        })
                      }
                    />
                  </div>

                  <Button
                    type="button"
                    variant="danger-outline"
                    icon={Trash2}
                    onClick={() => removeCartItemMutation(item.product_id)}
                  />
                </div>
              </article>
            ))
          )}

          <div className="rounded-3xl border border-secondary-100 bg-white p-6">
            <div className="flex items-start gap-3">
              <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-50 text-secondary-600">
                <MapPinned className="h-5 w-5" />
              </div>
              <div>
                <h2 className="text-lg font-semibold text-dark-900">Checkout Information</h2>
                <p className="mt-1 text-sm text-dark-500">
                  Fill in delivery and payment details before creating the order record.
                </p>
              </div>
            </div>

            {defaultAddress ? (
              <div className="mt-5 rounded-2xl border border-primary-100 bg-primary-50/60 p-4">
                <div className="flex flex-col gap-3 md:flex-row md:items-start md:justify-between">
                  <div>
                    <p className="text-sm font-semibold text-dark-900">
                      Autofilled from default address: {defaultAddress.label}
                    </p>
                    <p className="mt-1 text-sm text-dark-500">
                      {defaultAddress.recipient_name} | {defaultAddress.phone}
                    </p>
                    <p className="mt-1 text-sm text-dark-500">{defaultAddress.address}</p>
                  </div>
                  <Button type="link" link="/customer/profile" variant="primary-outline">
                    Manage Address Book
                  </Button>
                </div>
              </div>
            ) : (
              <div className="mt-5 rounded-2xl border border-dark-200 bg-light-50 p-4">
                <p className="text-sm text-dark-600">
                  No default address saved yet. You can still check out manually, or save one in
                  your profile.
                </p>
                <Button
                  type="link"
                  link="/customer/profile"
                  variant="secondary-outline"
                  className="mt-3"
                >
                  Open Customer Profile
                </Button>
              </div>
            )}

            <form className="mt-5 grid gap-4 md:grid-cols-2">
              <FormInput
                control={control}
                name="customer_phone"
                type="text"
                label="Phone Number"
                placeholder="0812xxxxxxx"
              />
              <FormInput
                control={control}
                name="courier_service"
                type="dropdown"
                label="Courier Service"
                placeholder="Select courier service"
                dropdownOptions={courierOptions}
              />
              <FormInput
                control={control}
                name="payment_method"
                type="dropdown"
                label="Payment Method"
                placeholder="Select payment method"
                dropdownOptions={paymentMethodOptions}
              />
              <div className="hidden md:block" />
              <FormInput
                control={control}
                name="shipping_address"
                type="textarea"
                label="Shipping Address"
                placeholder="Street, district, city, province, postal code"
                className="md:col-span-2"
              />
              <FormInput
                control={control}
                name="order_note"
                type="textarea"
                label="Order Note"
                placeholder="Optional note for courier or packing team"
                className="md:col-span-2"
              />
            </form>
          </div>
        </section>

        <aside className="space-y-4">
          <div className="rounded-3xl border border-primary-100 bg-dark-900 p-6 text-white">
            <p className="text-sm uppercase tracking-[0.2em] text-primary-300">Order Summary</p>
            <div className="mt-5 space-y-3 text-sm text-white/80">
              <div className="flex items-center justify-between">
                <span>Items subtotal</span>
                <span>{formatCurrency(itemSubtotal)}</span>
              </div>
              <div className="flex items-center justify-between">
                <span>Shipping protection</span>
                <span>{formatCurrency(shippingProtection)}</span>
              </div>
              <div className="flex items-center justify-between">
                <span>Estimated shipping</span>
                <span>{formatCurrency(estimatedShipping)}</span>
              </div>
            </div>
            <div className="mt-5 rounded-2xl border border-white/10 bg-white/5 p-4">
              <div className="flex items-start gap-3">
                <WalletCards className="mt-0.5 h-5 w-5 text-primary-300" />
                <div className="text-sm text-white/80">
                  <p className="font-semibold text-white">
                    Checkout details are saved with the order
                  </p>
                  <p className="mt-1">
                    Phone number stays in the dedicated field, while address and payment preferences
                    are recorded in order notes.
                  </p>
                </div>
              </div>
            </div>
            <div className="mt-5 border-t border-white/10 pt-5">
              <div className="flex items-center justify-between">
                <span className="text-sm text-white/70">Grand total</span>
                <span className="text-2xl font-semibold">{formatCurrency(grandTotal)}</span>
              </div>
            </div>
            <Button
              className="mt-6 w-full"
              icon={CreditCard}
              loading={isCheckoutPending}
              disabled={cartItems.length === 0}
              onClick={handleSubmit(onCheckout)}
            >
              {isCheckoutPending ? "Creating order..." : "Place Order"}
            </Button>
          </div>

          <div className="grid gap-3 rounded-3xl border border-secondary-100 bg-secondary-50/80 p-5">
            <div className="flex items-start gap-3">
              <ShieldCheck className="mt-0.5 h-5 w-5 text-secondary-600" />
              <div>
                <p className="font-semibold text-dark-900">Scoped to your account</p>
                <p className="text-sm text-dark-500">
                  New orders created from this cart are automatically attached to your signed-in
                  identity.
                </p>
              </div>
            </div>
            <div className="flex items-start gap-3">
              <Truck className="mt-0.5 h-5 w-5 text-secondary-600" />
              <div>
                <p className="font-semibold text-dark-900">Order tracking ready</p>
                <p className="text-sm text-dark-500">
                  After checkout, the order shows up immediately in your customer order pages.
                </p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </>
  );
};
