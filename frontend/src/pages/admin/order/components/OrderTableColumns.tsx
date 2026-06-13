import { Eye, Pencil } from "lucide-react";
import { Badge, Button, type TableColumn } from "../../../../components/ui";
import type { Order } from "../../../../interfaces/order";

interface OrderTableColumnsOptions {
  hasAccess: (code: string) => boolean;
  onUpdate: (order: Order) => void;
}

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

export const getOrderTableColumns = ({
  hasAccess,
  onUpdate,
}: OrderTableColumnsOptions): TableColumn<Order>[] => {
  return [
    {
      key: "order_number",
      header: "Order",
      className: "min-w-[220px]",
      render: (item) => (
        <div>
          <p className="font-semibold text-dark-900">{item.order_number}</p>
          <p className="mt-1 text-sm text-dark-500">{item.customer_name}</p>
          <p className="mt-1 text-xs text-dark-400">{item.channel}</p>
        </div>
      ),
    },
    {
      key: "status",
      header: "Order Status",
      className: "min-w-[140px]",
      render: (item) => <Badge variant={statusVariantMap[item.status]}>{item.status}</Badge>,
    },
    {
      key: "payment_status",
      header: "Payment",
      className: "min-w-[130px]",
      render: (item) => <Badge variant={paymentVariantMap[item.payment_status]}>{item.payment_status}</Badge>,
    },
    {
      key: "total_items",
      header: "Items",
      className: "min-w-[100px]",
      render: (item) => `${item.total_items} item(s)`,
    },
    {
      key: "total_amount",
      header: "Total",
      className: "min-w-[150px]",
      render: (item) => formatCurrency(item.total_amount),
    },
    {
      key: "created_at",
      header: "Created At",
      className: "min-w-[170px]",
    },
    {
      key: "actions",
      header: "Actions",
      className: "w-[1%] whitespace-nowrap",
      hidden: !hasAccess("U"),
      render: (item) => (
        <div className="flex items-center gap-2">
          <Button type="link" link={`/admin/sales/orders/${item.id}`} variant="primary-outline" icon={Eye}>
            Detail
          </Button>
          {hasAccess("U") ? (
            <Button type="button" variant="warning" icon={Pencil} onClick={() => onUpdate(item)}>
              Update
            </Button>
          ) : null}
        </div>
      ),
    },
  ];
};
