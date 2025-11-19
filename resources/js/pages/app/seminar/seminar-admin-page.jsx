import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
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
import { SeminarStatusDialog } from "./dialogs/seminar-status-dialog";

export default function SeminarAdminPage() {
    const { seminarList, flash } = usePage().props;
    const [dataSeminar, setDataSeminar] = React.useState(seminarList);

    const [sorting, setSorting] = React.useState([]);
    const [columnFilters, setColumnFilters] = React.useState([]);
    const [columnVisibility, setColumnVisibility] = React.useState({});
    const [rowSelection, setRowSelection] = React.useState({});

    // Status Dialog
    const [dataStatus, setDataStatus] = React.useState(null);
    const [isStatusDialogOpen, setIsStatusDialogOpen] = React.useState(false);

    React.useEffect(() => {
        if (flash.success) {
            router.reload({ only: ["seminarList"] });
            setIsStatusDialogOpen(false);
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
            id: "Dosen",
            accessorKey: "user",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === "asc")
                    }
                >
                    Dosen
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
                <div>
                    <span className="text-gray-400">
                        @{row.original.user?.username}
                    </span>
                    <br />
                    <span className="font-medium">
                        {row.original.user?.name}
                    </span>
                </div>
            ),
        },
        {
            id: "Nama Forum",
            accessorKey: "nama_forum",
            header: "Nama Forum",
            cell: ({ row }) => (
                <div className="font-medium">{row.original.nama_forum}</div>
            ),
        },
        {
            id: "Institusi",
            accessorKey: "institusi_penyelenggara",
            header: "Institusi",
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
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <Button variant="ghost" className="h-8 w-8 p-0">
                            <span className="sr-only">Open menu</span>
                            <Icon.IconDotsVertical />
                        </Button>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent align="end">
                        <DropdownMenuItem
                            onClick={() => {
                                setDataStatus(row.original);
                                setIsStatusDialogOpen(true);
                            }}
                        >
                            <Icon.IconPencil size={16} className="mr-2" />
                            Ubah Status
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
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
                                <span>Daftar Seminar (Admin)</span>
                            </div>
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

            <SeminarStatusDialog
                dataStatus={dataStatus}
                openDialog={isStatusDialogOpen}
                setOpenDialog={setIsStatusDialogOpen}
            />
        </AppLayout>
    );
}
