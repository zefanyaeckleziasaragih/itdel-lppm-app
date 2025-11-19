import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    Table,
    TableBody,
    TableCell,
    TableHead,
    TableHeader,
    TableRow,
} from "@/components/ui/table";
import AppLayout from "@/layouts/app-layout";
import { router, usePage } from "@inertiajs/react";
import * as Icon from "@tabler/icons-react";
import {
    flexRender,
    getCoreRowModel,
    getFilteredRowModel,
    getPaginationRowModel,
    getSortedRowModel,
    useReactTable,
} from "@tanstack/react-table";
import * as React from "react";
import { toast } from "sonner";
import { SeminarFormDialog } from "./dialogs/seminar-form-dialog";
import { SeminarDeleteDialog } from "./dialogs/seminar-delete-dialog";

export default function SeminarPage() {
    const { seminarList, flash } = usePage().props;
    const [dataSeminar, setDataSeminar] = React.useState(seminarList);

    const [sorting, setSorting] = React.useState([]);
    const [columnFilters, setColumnFilters] = React.useState([]);
    const [columnVisibility, setColumnVisibility] = React.useState({});
    const [rowSelection, setRowSelection] = React.useState({});

    // Form Dialog
    const [isFormDialogOpen, setIsFormDialogOpen] = React.useState(false);
    const [titleFormDialog, setTitleFormDialog] =
        React.useState("Tambah Seminar");
    const [dataEdit, setDataEdit] = React.useState(null);

    // Delete Dialog
    const [dataDelete, setDataDelete] = React.useState(null);
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = React.useState(false);

    React.useEffect(() => {
        if (flash.success) {
            router.reload({ only: ["seminarList"] });
            setIsFormDialogOpen(false);
            setIsDeleteDialogOpen(false);
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    React.useEffect(() => {
        setDataSeminar(seminarList);
    }, [seminarList]);

    const columns = [
        {
            id: "Nama Forum",
            accessorKey: "nama_forum",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === "asc")
                    }
                >
                    Nama Forum
                    {column.getIsSorted() ? (
                        column.getIsSorted() === "asc" ? (
                            <Icon.IconArrowUp size={16} />
                        ) : (
                            <Icon.IconArrowDown size={16} />
                        )
                    ) : (
                        <Icon.IconArrowsDownUp />
                    )}
                </Button>
            ),
            cell: ({ row }) => (
                <div className="font-medium">{row.original.nama_forum}</div>
            ),
        },
        {
            id: "Institusi",
            accessorKey: "institusi_penyelenggara",
            header: "Institusi Penyelenggara",
        },
        {
            id: "Waktu",
            accessorKey: "waktu_pelaksanaan",
            header: "Waktu Pelaksanaan",
            cell: ({ row }) => (
                <div>
                    {new Date(
                        row.original.waktu_pelaksanaan
                    ).toLocaleDateString("id-ID", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                    })}
                </div>
            ),
        },
        {
            id: "Status",
            accessorKey: "status",
            header: "Status",
            cell: ({ row }) => (
                <Badge
                    variant={
                        row.original.status === "Sudah Dicairkan"
                            ? "default"
                            : "secondary"
                    }
                >
                    {row.original.status}
                </Badge>
            ),
        },
        {
            id: "Tindakan",
            header: "Tindakan",
            cell: ({ row }) => (
                <div className="flex gap-2">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => {
                            setDataEdit(row.original);
                            setTitleFormDialog("Ubah Seminar");
                            setIsFormDialogOpen(true);
                        }}
                    >
                        <Icon.IconPencil size={16} />
                    </Button>
                    <Button
                        variant="outline"
                        size="sm"
                        className="border-red-500 text-red-500"
                        onClick={() => {
                            setDataDelete(row.original);
                            setIsDeleteDialogOpen(true);
                        }}
                    >
                        <Icon.IconTrash size={16} />
                    </Button>
                </div>
            ),
        },
    ];

    const table = useReactTable({
        data: dataSeminar,
        columns,
        onSortingChange: setSorting,
        onColumnFiltersChange: setColumnFilters,
        getCoreRowModel: getCoreRowModel(),
        getPaginationRowModel: getPaginationRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        onColumnVisibilityChange: setColumnVisibility,
        onRowSelectionChange: setRowSelection,
        state: {
            sorting,
            columnFilters,
            columnVisibility,
            rowSelection,
        },
    });

    return (
        <AppLayout>
            <Card className="h-full">
                <CardHeader>
                    <CardTitle className="flex items-center">
                        <div className="flex-1">
                            <div className="flex items-center">
                                <Icon.IconAward className="inline mr-2" />
                                <span>Daftar Seminar Saya</span>
                            </div>
                        </div>

                        <div className="flex items-center space-x-2">
                            <Button
                                variant="default"
                                onClick={() => {
                                    setDataEdit(null);
                                    setTitleFormDialog("Tambah Seminar");
                                    setIsFormDialogOpen(true);
                                }}
                            >
                                <Icon.IconPlus className="mr-2" />
                                Ajukan Seminar
                            </Button>
                        </div>
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div className="overflow-hidden rounded-md border">
                        <Table>
                            <TableHeader className="bg-primary">
                                {table.getHeaderGroups().map((headerGroup) => (
                                    <TableRow key={headerGroup.id}>
                                        {headerGroup.headers.map((header) => (
                                            <TableHead
                                                key={header.id}
                                                className="text-primary-foreground"
                                            >
                                                {header.isPlaceholder
                                                    ? null
                                                    : flexRender(
                                                          header.column
                                                              .columnDef.header,
                                                          header.getContext()
                                                      )}
                                            </TableHead>
                                        ))}
                                    </TableRow>
                                ))}
                            </TableHeader>
                            <TableBody>
                                {table.getRowModel().rows?.length ? (
                                    table.getRowModel().rows.map((row) => (
                                        <TableRow key={row.id}>
                                            {row
                                                .getVisibleCells()
                                                .map((cell) => (
                                                    <TableCell key={cell.id}>
                                                        {flexRender(
                                                            cell.column
                                                                .columnDef.cell,
                                                            cell.getContext()
                                                        )}
                                                    </TableCell>
                                                ))}
                                        </TableRow>
                                    ))
                                ) : (
                                    <TableRow>
                                        <TableCell
                                            colSpan={columns.length}
                                            className="h-24 text-center"
                                        >
                                            Belum ada data seminar.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>
                </CardContent>
            </Card>

            <SeminarFormDialog
                dataEdit={dataEdit}
                dialogTitle={titleFormDialog}
                openDialog={isFormDialogOpen}
                setOpenDialog={setIsFormDialogOpen}
            />

            <SeminarDeleteDialog
                dataDelete={dataDelete}
                openDialog={isDeleteDialogOpen}
                setOpenDialog={setIsDeleteDialogOpen}
            />
        </AppLayout>
    );
}
