import { Pencil, Trash2 } from "lucide-react";
import { Badge, Button, type TableColumn } from "../../../../components/ui";
import type { Article } from "../../../../interfaces/article";

interface ArticleTableColumnsOptions {
  hasAccess: (code: string) => boolean;
  onEdit: (article: Article) => void;
  onDelete: (article: Article) => void;
}

const baseArticleColumns: TableColumn<Article>[] = [
  {
    key: "title",
    header: "Article",
    className: "min-w-[280px]",
    render: (item) => (
      <div>
        <p className="font-semibold text-dark-900">{item.title}</p>
        <p className="mt-1 text-xs uppercase tracking-[0.16em] text-dark-400">{item.slug}</p>
        <p className="mt-1 text-sm text-dark-500">{item.excerpt || "-"}</p>
      </div>
    ),
  },
  {
    key: "category",
    header: "Category",
    className: "min-w-[160px]",
    render: (item) => <Badge variant="secondary-outline">{item.category}</Badge>,
  },
  {
    key: "author_name",
    header: "Author",
    className: "min-w-[150px]",
    render: (item) => item.author_name || "-",
  },
  {
    key: "status",
    header: "Status",
    className: "min-w-[130px]",
    render: (item) => (
      <Badge variant={item.status === "published" ? "success-outline" : "warning-outline"}>
        {item.status}
      </Badge>
    ),
  },
  {
    key: "published_at",
    header: "Published At",
    className: "min-w-[170px]",
    render: (item) => item.published_at || "-",
  },
];

export const getArticleTableColumns = ({
  hasAccess,
  onEdit,
  onDelete,
}: ArticleTableColumnsOptions): TableColumn<Article>[] => {
  return [
    ...baseArticleColumns,
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
