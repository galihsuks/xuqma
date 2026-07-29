import { Pencil, Trash2 } from "lucide-react";
import { Badge, Button, type TableColumn } from "../../../../components/ui";
import type { ProductCategory } from "../../../../interfaces/productCategory";

interface CategoryTableColumnsOptions {
  hasAccess: (code: string) => boolean;
  onEdit: (category: ProductCategory) => void;
  onDelete: (category: ProductCategory) => void;
}

export const getCategoryTableColumns = ({
  hasAccess,
  onEdit,
  onDelete,
}: CategoryTableColumnsOptions): TableColumn<ProductCategory>[] => {
  return [
    {
      key: "name",
      header: "Category",
      className: "min-w-[260px]",
      render: (item) => (
        <div>
          <p className="font-semibold text-dark-900">{item.name}</p>
          <p className="mt-1 text-xs uppercase tracking-[0.16em] text-dark-400">{item.slug}</p>
          <p className="mt-1 text-sm text-dark-500">{item.description || "-"}</p>
        </div>
      ),
    },
    {
      key: "icon",
      header: "Icon",
      className: "min-w-[160px]",
      render: (item) => item.icon || "-",
    },
    {
      key: "display",
      header: "Storefront",
      className: "min-w-[140px]",
      render: (item) => (
        <Badge
          variant={
            item.display === true || item.display === "1" ? "success-outline" : "light-outline"
          }
        >
          {item.display === true || item.display === "1" ? "Visible" : "Hidden"}
        </Badge>
      ),
    },
    {
      key: "sort",
      header: "Sort",
      className: "min-w-[110px]",
      align: "center",
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
