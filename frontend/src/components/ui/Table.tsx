import { ChevronLeft, ChevronRight, ChevronsLeft, ChevronsRight } from "lucide-react";
import { cn } from "../../utils/cn";
import type { ReactNode } from "react";
import type { ApiPagination } from "../../interfaces/api";
import { Button } from "./Button";
import { useCollapseDesktopSidebar } from "../../store/layoutStore";

export interface TableColumn<T> {
  key: keyof T | string;
  header: string;
  align?: "left" | "center" | "right";
  className?: string;
  hidden?: boolean;
  render?: (item: T, index: number) => ReactNode;
}

interface TableProps<T> {
  columns: TableColumn<T>[];
  data: T[];
  className?: string;
  tableClassName?: string;
  loading?: boolean;
  emptyText?: string;
  rowKey?: (item: T, index: number) => string;
  pagination?: ApiPagination;
  onPageChange?: (page: number) => void;
  showNumber?: boolean;
}

const buildPaginationItems = (currentPage: number, totalPages: number): Array<number | "..."> => {
  if (totalPages <= 3) {
    return Array.from({ length: totalPages }, (_, index) => index + 1);
  }

  if (currentPage <= 2) {
    return [1, 2, 3, ...(totalPages > 3 ? (["..."] as const) : [])];
  }

  if (currentPage >= totalPages - 1) {
    return [
      ...(totalPages > 3 ? (["..."] as const) : []),
      totalPages - 2,
      totalPages - 1,
      totalPages,
    ];
  }

  return ["...", currentPage - 1, currentPage, currentPage + 1, "..."];
};

const alignClassMap: Record<NonNullable<TableColumn<unknown>["align"]>, string> = {
  left: "text-left",
  center: "text-center",
  right: "text-right",
};

export const Table = <T,>({
  columns,
  data,
  className,
  tableClassName,
  loading = false,
  emptyText = "No data available.",
  rowKey,
  pagination,
  onPageChange,
  showNumber = true,
}: TableProps<T>) => {
  const collapseDesktopSidebar = useCollapseDesktopSidebar();
  const visibleColumns = columns.filter((column) => !column.hidden);
  const columnCount = visibleColumns.length + (showNumber ? 1 : 0);

  const paginationItems = pagination
    ? buildPaginationItems(pagination.page, pagination.total_pages)
    : [];

  return (
    <>
      <div className={cn("overflow-hidden rounded-2xl border border-dark-200 bg-white", className)}>
        <div
          className={cn(
            "transition-all duration-300 overflow-x-auto w-[calc(100vw-var(--spacing)*9-var(--spacing)*12)]",
            collapseDesktopSidebar
              ? "md:w-[calc(100vw-var(--spacing)*12-var(--spacing)*4-80px-var(--spacing)*16)] max-w-[calc(1400px-var(--spacing)*16-80px-var(--spacing)*1-var(--spacing)*16)]"
              : "md:w-[calc(100vw-var(--spacing)*12-var(--spacing)*4-280px-var(--spacing)*16)] max-w-[calc(1400px-var(--spacing)*16-280px-var(--spacing)*1-var(--spacing)*16)]",
          )}
        >
          <table className={cn("min-w-full divide-y divide-dark-200", tableClassName)}>
            <thead className="bg-primary-50/70">
              <tr>
                {showNumber ? (
                  <th className="text-center w-[1%] whitespace-nowrap px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-dark-700">
                    No
                  </th>
                ) : null}
                {visibleColumns.map((column) => (
                  <th
                    key={String(column.key)}
                    className={cn(
                      "px-4 py-3 text-xs font-semibold uppercase tracking-wider text-dark-700",
                      alignClassMap[column.align ?? "left"],
                      column.className,
                    )}
                  >
                    {column.header}
                  </th>
                ))}
              </tr>
            </thead>
            <tbody className="divide-y divide-dark-100">
              {loading ? (
                <tr>
                  <td className="px-4 py-6 text-sm text-dark-500" colSpan={columnCount}>
                    Loading...
                  </td>
                </tr>
              ) : data.length === 0 ? (
                <tr>
                  <td className="px-4 py-6 text-sm text-dark-500" colSpan={columnCount}>
                    {emptyText}
                  </td>
                </tr>
              ) : (
                data.map((item, index) => (
                  <tr key={rowKey ? rowKey(item, index) : `${index}`}>
                    {showNumber ? (
                      <td className="text-center px-4 py-3 text-sm font-medium text-dark-500">
                        {pagination
                          ? (pagination.page - 1) * pagination.page_size + index + 1
                          : index + 1}
                      </td>
                    ) : null}
                    {visibleColumns.map((column) => (
                      <td
                        key={String(column.key)}
                        className={cn(
                          "px-4 py-3 text-sm text-dark-700",
                          alignClassMap[column.align ?? "left"],
                          column.className,
                        )}
                      >
                        {column.render
                          ? column.render(item, index)
                          : String((item as Record<string, unknown>)[String(column.key)] ?? "")}
                      </td>
                    ))}
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {pagination ? (
        <div
          className={cn(
            "flex flex-col-reverse gap-3 border-t border-dark-100 py-4 items-center justify-between",
            collapseDesktopSidebar ? "md:flex-row" : "lg:flex-row",
          )}
        >
          <p className="text-sm text-dark-500">
            Page {pagination.page} of {pagination.total_pages} with {pagination.total_items} total
            item(s).
          </p>
          <div className="flex flex-wrap items-center gap-2">
            <Button
              type="button"
              variant="light-outline"
              icon={ChevronsLeft}
              disabled={!pagination.has_prev}
              onClick={() => onPageChange?.(1)}
            />
            <div className={cn("hidden", collapseDesktopSidebar ? "lg:block" : "xl:block")}>
              <Button
                type="button"
                variant="light-outline"
                icon={ChevronLeft}
                disabled={!pagination.has_prev}
                onClick={() => onPageChange?.(pagination.page - 1)}
              />
            </div>
            {paginationItems.map((item, index) =>
              item === "..." ? (
                <span
                  key={`ellipsis-${index}`}
                  className={cn(
                    "hidden h-10 items-center justify-center px-2 text-sm font-semibold text-dark-400 sm:inline-flex lg:inline-flex",
                    collapseDesktopSidebar ? "" : "md:hidden",
                  )}
                >
                  ...
                </span>
              ) : (
                <Button
                  key={`page-${item}`}
                  type="button"
                  variant={item === pagination.page ? "primary" : "light-outline"}
                  className="min-w-10 px-3"
                  onClick={() => onPageChange?.(item)}
                >
                  {item}
                </Button>
              ),
            )}
            <div className={cn("hidden", collapseDesktopSidebar ? "lg:block" : "xl:block")}>
              <Button
                type="button"
                variant="light-outline"
                icon={ChevronRight}
                disabled={!pagination.has_next}
                onClick={() => onPageChange?.(pagination.page + 1)}
              />
            </div>
            <Button
              type="button"
              variant="light-outline"
              icon={ChevronsRight}
              disabled={!pagination.has_next}
              onClick={() => onPageChange?.(pagination.total_pages)}
            />
          </div>
        </div>
      ) : null}
    </>
  );
};
