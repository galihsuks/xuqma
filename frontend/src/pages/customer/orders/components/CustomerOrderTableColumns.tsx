import { Eye } from "lucide-react";
import { Badge, Button, type TableColumn } from "../../../../components/ui";
import type { Order } from "../../../../interfaces/order";

const statusVariantMap = {
  "Waiting Payment": "warning-outline",
  Processing: "info-outline",
  Packed: "primary-outline",
  Shipped: "secondary-outline",
  Completed: "success-outline",
  Cancelled: "danger-outline",
} as const;

const formatCurrency = (value: number | string) => {
  const amount = typeof value === "number" ? value : Number(value);
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number.isNaN(amount) ? 0 : amount);
};

export const getCustomerOrderTableColumns = (): TableColumn<Order>[] => {
  return [
    {
      key: "order_number",
      header: "Order",
      render: (item) => (
        <div>
          <p className="font-semibold text-dark-900">{item.order_number}</p>
          <p className="text-xs text-dark-400">{item.total_items} item(s)</p>
        </div>
      ),
    },
    {
      key: "created_at",
      header: "Placed At",
    },
    {
      key: "status",
      header: "Status",
      render: (item) => <Badge variant={statusVariantMap[item.status]}>{item.status}</Badge>,
    },
    {
      key: "total_amount",
      header: "Total",
      render: (item) => formatCurrency(item.total_amount),
    },
    {
      key: "actions",
      header: "Actions",
      className: "w-[1%] whitespace-nowrap",
      render: (item) => (
        <Button type="link" link={`/customer/orders/${item.id}`} variant="primary-outline" icon={Eye}>
          Detail
        </Button>
      ),
    },
  ];
};
