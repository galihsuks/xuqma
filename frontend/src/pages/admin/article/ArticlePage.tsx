import { FilePenLine, Plus, Search } from "lucide-react";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Button, FilterGrid, FormInput, Table } from "../../../components/ui";
import { useArticleListQuery } from "../../../api/article/articleQuery";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { useDebounce } from "../../../hooks/useDebounce";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { Article } from "../../../interfaces/article";
import InternalServerError from "../../../components/templates/InternalServerError";
import { useHasAccess } from "../../../store/accessControlStore";
import { getArticleTableColumns } from "./components/ArticleTableColumns";
import { ArticleDeleteModal } from "./components/ArticleDeleteModal";
import { ArticleFormModal } from "./components/ArticleFormModal";

interface ArticleFilterSchemaType {
  keywords: string;
  status: string;
}

const articleStatusOptions = [
  { value: "", label: "All statuses" },
  { value: "draft", label: "Draft" },
  { value: "published", label: "Published" },
];

export const ArticlePage = () => {
  usePageTitle("Article Management");

  const hasAccess = useHasAccess();
  const [page, setPage] = useState(1);
  const [articleFormMode, setArticleFormMode] = useState<"create" | "edit" | null>(null);
  const [selectedArticleId, setSelectedArticleId] = useState<string | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{
    id: string;
    title: string;
    slug: string;
  } | null>(null);
  const { control, watch } = useForm<ArticleFilterSchemaType>({
    defaultValues: {
      keywords: "",
      status: "",
    },
  });
  const keywords = watch("keywords");
  const status = watch("status");
  const debouncedKeywords = useDebounce(keywords, 300);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeywords, status]);

  const { data, isPending, error } = useArticleListQuery({
    page,
    page_size: DEFAULT_PAGE_SIZE,
    keywords: debouncedKeywords || undefined,
    status: (status as "" | "draft" | "published") || undefined,
  });

  if (error) {
    return <InternalServerError />;
  }

  const articleColumns = getArticleTableColumns({
    hasAccess,
    onEdit: (article: Article) => {
      setSelectedArticleId(article.id);
      setArticleFormMode("edit");
    },
    onDelete: (article: Article) =>
      setDeleteTarget({
        id: article.id,
        title: article.title,
        slug: article.slug,
      }),
  });

  return (
    <>
      <PageHeader
        title="Article Management"
        subtitle="Track storefront content from the backend API and monitor which pieces are published for SEO traffic."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Content", route: undefined },
          { label: "Articles", route: undefined },
        ]}
        rightElement={
          hasAccess("C") ? (
            <Button
              type="button"
              variant="primary"
              icon={Plus}
              onClick={() => {
                setSelectedArticleId(null);
                setArticleFormMode("create");
              }}
            >
              Create Article
            </Button>
          ) : undefined
        }
      />

      <div className="mb-6 rounded-3xl border border-secondary-100 bg-secondary-50/70 p-5">
        <div className="flex items-start gap-3">
          <FilePenLine className="mt-0.5 h-5 w-5 text-secondary-600" />
          <div>
            <p className="font-semibold text-dark-900">SEO content pipeline is now API-backed</p>
            <p className="mt-1 text-sm text-dark-500">
              Article list data is served from the CodeIgniter backend so the admin area and storefront can evolve from the same source.
            </p>
          </div>
        </div>
      </div>

      <FilterGrid>
        <FormInput
          control={control}
          name="keywords"
          type="text"
          icon={Search}
          placeholder="Search by title, slug, category, or author..."
        />
        <FormInput
          control={control}
          name="status"
          type="dropdown"
          placeholder="Filter by status"
          dropdownOptions={articleStatusOptions}
        />
      </FilterGrid>

      <Table
        columns={articleColumns}
        data={data?.data ?? []}
        loading={isPending}
        emptyText="No articles available yet."
        pagination={data?.pagination}
        onPageChange={setPage}
      />

      <ArticleFormModal
        open={articleFormMode !== null}
        mode={articleFormMode ?? "create"}
        articleId={selectedArticleId}
        onClose={() => {
          setSelectedArticleId(null);
          setArticleFormMode(null);
        }}
      />

      <ArticleDeleteModal
        open={deleteTarget !== null}
        target={deleteTarget}
        onClose={() => setDeleteTarget(null)}
      />
    </>
  );
};
