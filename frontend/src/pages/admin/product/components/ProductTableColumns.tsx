import { Pencil, Trash2 } from "lucide-react";
import { Badge, Button, type TableColumn } from "../../../../components/ui";
import type { Product } from "../../../../interfaces/product";

interface ProductTableColumnsOptions {
  hasAccess: (code: string) => boolean;
  onEdit: (product: Product) => void;
  onDelete: (product: Product) => void;
}

const formatCurrency = (value: number | string) => {
  const amount = typeof value === "number" ? value : Number(value);
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number.isNaN(amount) ? 0 : amount);
};

export const getProductTableColumns = ({
  hasAccess,
  onEdit,
  onDelete,
}: ProductTableColumnsOptions): TableColumn<Product>[] => {
  return [
    {
      key: "name",
      header: "Product",
      className: "min-w-[260px]",
      render: (item) => (
        <div>
          <p className="font-semibold text-dark-900">{item.name}</p>
          <p className="mt-1 text-xs uppercase tracking-[0.16em] text-dark-400">{item.sku}</p>
          <p className="mt-1 text-sm text-dark-500">{item.highlight || item.summary || "-"}</p>
        </div>
      ),
    },
    {
      key: "category_name",
      header: "Category",
      className: "min-w-[180px]",
      render: (item) => <Badge variant="secondary-outline">{item.category_name}</Badge>,
    },
    {
      key: "price",
      header: "Price",
      className: "min-w-[140px]",
      render: (item) => formatCurrency(item.price),
    },
    {
      key: "stock",
      header: "Stock",
      className: "min-w-[120px]",
      render: (item) => `${item.stock} unit(s)`,
    },
    {
      key: "stock_badge",
      header: "Availability",
      className: "min-w-[140px]",
      render: (item) => (
        <Badge
          variant={
            item.stock_badge === "Ready Stock"
              ? "success-outline"
              : item.stock_badge === "Pre Order"
                ? "warning-outline"
                : "primary-outline"
          }
        >
          {item.stock_badge}
        </Badge>
      ),
    },
    {
      key: "actions",
      header: "Actions",
      className: "w-[1%] whitespace-nowrap",
      hidden: !hasAccess("U") && !hasAccess("D"),
      render: (item) => (
        <div className="flex items-center gap-2">
          {hasAccess("U") ? (
            <Button type="button" variant="warning" icon={Pencil} onClick={() => onEdit(item)} />
          ) : null}
          {hasAccess("D") ? (
            <Button type="button" variant="danger" icon={Trash2} onClick={() => onDelete(item)} />
          ) : null}
        </div>
      ),
    },
  ];
};
