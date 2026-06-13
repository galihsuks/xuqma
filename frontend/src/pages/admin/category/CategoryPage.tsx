import { FolderKanban, Plus, Search } from "lucide-react";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { PageHeader } from "../../../components/layout/PageHeader";
import { Button, FilterGrid, FormInput, Table } from "../../../components/ui";
import { useProductCategoryListQuery } from "../../../api/productCategory/productCategoryQuery";
import { DEFAULT_PAGE_SIZE } from "../../../constants";
import { useDebounce } from "../../../hooks/useDebounce";
import { usePageTitle } from "../../../hooks/usePageTitle";
import type { ProductCategory } from "../../../interfaces/productCategory";
import InternalServerError from "../../../components/templates/InternalServerError";
import { useHasAccess } from "../../../store/accessControlStore";
import { getCategoryTableColumns } from "./components/CategoryTableColumns";
import { CategoryDeleteModal } from "./components/CategoryDeleteModal";
import { CategoryFormModal } from "./components/CategoryFormModal";

interface CategoryFilterSchemaType {
  keywords: string;
}

export const CategoryPage = () => {
  usePageTitle("Category Management");

  const hasAccess = useHasAccess();
  const [page, setPage] = useState(1);
  const [categoryFormMode, setCategoryFormMode] = useState<"create" | "edit" | null>(null);
  const [selectedCategoryId, setSelectedCategoryId] = useState<string | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<{
    id: string;
    name: string;
    slug: string;
  } | null>(null);
  const { control, watch } = useForm<CategoryFilterSchemaType>({
    defaultValues: {
      keywords: "",
    },
  });
  const keywords = watch("keywords");
  const debouncedKeywords = useDebounce(keywords, 300);

  useEffect(() => {
    setPage(1);
  }, [debouncedKeywords]);

  const { data, isPending, error } = useProductCategoryListQuery({
    page,
    page_size: DEFAULT_PAGE_SIZE,
    keywords: debouncedKeywords || undefined,
  });

  if (error) {
    return <InternalServerError />;
  }

  const categoryColumns = getCategoryTableColumns({
    hasAccess,
    onEdit: (category: ProductCategory) => {
      setSelectedCategoryId(category.id);
      setCategoryFormMode("edit");
    },
    onDelete: (category: ProductCategory) =>
      setDeleteTarget({
        id: category.id,
        name: category.name,
        slug: category.slug,
      }),
  });

  return (
    <>
      <PageHeader
        title="Category Management"
        subtitle="Control storefront groupings for IT products such as accessories, audio gear, and computer components."
        breadcrumbs={[
          { label: "Admin", route: undefined },
          { label: "Catalog", route: undefined },
          { label: "Categories", route: undefined },
        ]}
        rightElement={
          hasAccess("C") ? (
            <Button
              type="button"
              variant="primary"
              icon={Plus}
              onClick={() => {
                setSelectedCategoryId(null);
                setCategoryFormMode("create");
              }}
            >
              Add Category
            </Button>
          ) : undefined
        }
      />

      <div className="mb-6 rounded-3xl border border-secondary-100 bg-gradient-to-r from-secondary-50 via-white to-primary-50 p-5">
        <div className="flex items-start gap-3">
          <div className="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-secondary-600 text-white">
            <FolderKanban className="h-5 w-5" />
          </div>
          <div>
            <p className="font-semibold text-dark-900">Shared category source</p>
            <p className="mt-1 text-sm text-dark-500">
              Categories created here are reused by the product admin flow and the CodeIgniter storefront.
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
          placeholder="Search by category name, slug, or description..."
        />
      </FilterGrid>

      <Table
        columns={categoryColumns}
        data={data?.data ?? []}
        loading={isPending}
        emptyText="No categories available yet."
        pagination={data?.pagination}
        onPageChange={setPage}
      />

      <CategoryFormModal
        open={categoryFormMode !== null}
        mode={categoryFormMode ?? "create"}
        categoryId={selectedCategoryId}
        onClose={() => {
          setSelectedCategoryId(null);
          setCategoryFormMode(null);
        }}
      />

      <CategoryDeleteModal
        open={deleteTarget !== null}
        target={deleteTarget}
        onClose={() => setDeleteTarget(null)}
      />
    </>
  );
};
