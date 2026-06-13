import { CircleDollarSign, PackageSearch, Pencil, ShieldCheck, UserRound } from "lucide-react";
import { useState } from "react";
import { useParams } from "react-router-dom";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Badge, Button } from "../../../components/ui";
import { useOrderDetailQuery } from "../../../api/order/orderQuery";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { Order } from "../../../interfaces/order";
import InternalServerError from "../../../components/templates/InternalServerError";
import { useHasAccess } from "../../../store/accessControlStore";
import { OrderStatusModal } from "./components/OrderStatusModal";

const statusVariantMap: Record<
  Order["status"],
  | "warning-outline"
  | "info-outline"
  | "primary-outline"
  | "secondary-outline"
  | "success-outline"
  | "danger-outline"
> = {
  "Waiting Payment": "warning-outline",
  Processing: "info-outline",
  Packed: "primary-outline",
  Shipped: "secondary-outline",
  Completed: "success-outline",
  Cancelled: "danger-outline",
};

const paymentVariantMap: Record<
  Order["payment_status"],
  "warning-outline" | "success-outline" | "danger-outline"
> = {
  Unpaid: "warning-outline",
  Paid: "success-outline",
  Refunded: "danger-outline",
};

const formatCurrency = (value: number | string) => {
  const amount = typeof value === "number" ? value : Number(value);
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number.isNaN(amount) ? 0 : amount);
};

export const OrderDetailPage = () => {
  const params = useParams();
  const orderId = params.id ?? "";
  const hasAccess = useHasAccess();
  const [openStatusModal, setOpenStatusModal] = useState(false);

  usePageTitle("Order Detail");

  const { data, isPending, error } = useOrderDetailQuery(orderId);
  const order = data?.data;

  if (error) {
    return <InternalServerError />;
  }

  return (
    <>
      <PageHeader
        showGoBack
        title={order ? order.order_number : "Order Detail"}
        subtitle="Inspect item breakdown, customer identity, and current payment or fulfillment state from the live backend record."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Sales", route: undefined },
          { label: "Orders", route: "/admin/sales/orders" },
          { label: "Detail", route: undefined },
        ]}
        rightElement={
          <div className="flex flex-wrap gap-2">
            {hasAccess("U") ? (
              <Button type="button" variant="warning" icon={Pencil} onClick={() => setOpenStatusModal(true)}>
                Update Status
              </Button>
            ) : null}
            <Button type="link" link="/admin/sales/orders" variant="primary-outline">
              Back to Orders
            </Button>
          </div>
        }
      />

      {isPending || !order ? (
        <div className="grid gap-4">
          {Array.from({ length: 3 }).map((_, index) => (
            <div key={index} className="h-28 rounded-3xl bg-light-100" />
          ))}
        </div>
      ) : (
        <div className="space-y-6">
          <div className="grid gap-4 md:grid-cols-4">
            <div className="rounded-3xl border border-primary-100 bg-primary-50/70 p-5">
              <PackageSearch className="h-5 w-5 text-primary-600" />
              <p className="mt-4 text-sm text-dark-500">Fulfillment status</p>
              <div className="mt-2">
                <Badge variant={statusVariantMap[order.status]}>{order.status}</Badge>
              </div>
            </div>
            <div className="rounded-3xl border border-warning-100 bg-warning-50/70 p-5">
              <CircleDollarSign className="h-5 w-5 text-warning-600" />
              <p className="mt-4 text-sm text-dark-500">Payment status</p>
              <div className="mt-2">
                <Badge variant={paymentVariantMap[order.payment_status]}>{order.payment_status}</Badge>
              </div>
            </div>
            <div className="rounded-3xl border border-secondary-100 bg-secondary-50/70 p-5">
              <UserRound className="h-5 w-5 text-secondary-600" />
              <p className="mt-4 text-sm text-dark-500">Customer</p>
              <p className="mt-1 text-lg font-semibold text-dark-900">{order.customer_name}</p>
            </div>
            <div className="rounded-3xl border border-success-100 bg-success-50/70 p-5">
              <ShieldCheck className="h-5 w-5 text-success-600" />
              <p className="mt-4 text-sm text-dark-500">Grand total</p>
              <p className="mt-1 text-2xl font-semibold text-dark-900">{formatCurrency(order.total_amount)}</p>
            </div>
          </div>

          <div className="grid gap-6 xl:grid-cols-[1.15fr_0.85fr]">
            <section className="rounded-3xl border border-dark-200 bg-white p-6">
              <h2 className="text-lg font-semibold text-dark-900">Order item breakdown</h2>
              <p className="mt-1 text-sm text-dark-500">
                Backend-generated item rows and calculated subtotals.
              </p>

              <div className="mt-5 space-y-4">
                {(order.items ?? []).map((item) => (
                  <article key={item.id} className="rounded-2xl border border-dark-100 bg-light-50 p-4">
                    <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                      <div>
                        <p className="font-semibold text-dark-900">{item.product_name}</p>
                        <p className="mt-1 text-sm text-dark-500">
                          Product ID: {item.product_id || "-"} | Qty: {item.qty}
                        </p>
                      </div>
                      <div className="text-left md:text-right">
                        <p className="text-sm text-dark-500">{formatCurrency(item.unit_price)} each</p>
                        <p className="text-lg font-semibold text-primary-700">{formatCurrency(item.subtotal)}</p>
                      </div>
                    </div>
                  </article>
                ))}
              </div>
            </section>

            <aside className="space-y-4">
              <div className="rounded-3xl border border-dark-200 bg-dark-900 p-6 text-white">
                <p className="text-xs font-semibold uppercase tracking-[0.18em] text-secondary-300">
                  Customer Snapshot
                </p>
                <div className="mt-5 space-y-3 text-sm text-white/80">
                  <div className="flex items-center justify-between gap-3">
                    <span>Customer name</span>
                    <span className="text-right">{order.customer_name}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Email</span>
                    <span className="text-right">{order.customer_email || "-"}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Phone</span>
                    <span className="text-right">{order.customer_phone || "-"}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>User ID</span>
                    <span className="text-right">{order.user_id || "-"}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Channel</span>
                    <span className="text-right">{order.channel}</span>
                  </div>
                  <div className="flex items-center justify-between gap-3">
                    <span>Created at</span>
                    <span className="text-right">{order.created_at || "-"}</span>
                  </div>
                </div>
              </div>

              <div className="rounded-3xl border border-primary-100 bg-primary-50/70 p-5">
                <p className="font-semibold text-dark-900">Notes</p>
                <p className="mt-2 text-sm text-dark-500">{order.notes || "No additional notes."}</p>
              </div>
            </aside>
          </div>
        </div>
      )}

      <OrderStatusModal
        open={openStatusModal}
        orderId={orderId}
        onClose={() => setOpenStatusModal(false)}
      />
    </>
  );
};
