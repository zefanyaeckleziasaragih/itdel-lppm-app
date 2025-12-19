import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Checkbox } from "@/components/ui/checkbox";
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import {
    InputGroup,
    InputGroupAddon,
    InputGroupInput,
} from "@/components/ui/input-group";
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
import { ChevronDown } from "lucide-react";
import * as React from "react";
import { toast } from "sonner";
import { HakAksesChangeDialog } from "./dialogs/change-dialog";
import { HakAksesDeleteDialog } from "./dialogs/delete-dialog";
import { HakAksesDeleteSelectedDialog } from "./dialogs/delete-selected-dialog";

export default function HakAksesPage() {
    const { aksesList = [], flash = {} } = usePage().props;

    const [search, setSearch] = React.useState("");
    const [dataAkses, setDataAkses] = React.useState(aksesList);
    const [titleChangeDialog, setTitleChangeDialog] =
        React.useState("Tambah Hak Akses");

    const [sorting, setSorting] = React.useState([]);
    const [columnFilters, setColumnFilters] = React.useState([]);
    const [columnVisibility, setColumnVisibility] = React.useState({});
    const [rowSelection, setRowSelection] = React.useState({});

    // Data Edit
    const [isChangeDialogOpen, setIsChangeDialogOpen] = React.useState(false);
    const [dataEdit, setDataEdit] = React.useState(null);

    // Data Delete
    const [dataDelete, setDataDelete] = React.useState(null);
    const [isDeleteDialogOpen, setIsDeleteDialogOpen] = React.useState(false);

    // Data Delete Selected
    const [dataDeleteSelected, setDataDeleteSelected] = React.useState(null);
    const [isDeleteSelectedDialogOpen, setIsDeleteSelectedDialogOpen] =
        React.useState(false);

    // Flash messages
    React.useEffect(() => {
        if (flash?.success) {
            router.reload({ only: ["aksesList"] });
            setIsChangeDialogOpen(false);
            setIsDeleteDialogOpen(false);
            setIsDeleteSelectedDialogOpen(false);
            toast.success(flash.success);
        }
        if (flash?.error) {
            toast.error(flash.error);
        }
    }, [flash]);

    // Search filter (AMAN walau user null)
    React.useEffect(() => {
        if (!search) {
            setDataAkses(aksesList);
            return;
        }

        const searchLower = search.toLowerCase();

        const filteredData = (aksesList ?? []).filter((item) => {
            const user = item?.user ?? null;
            const name = (user?.name ?? "").toLowerCase();
            const username = (user?.username ?? "").toLowerCase();

            const aksesArr = Array.isArray(item?.data_akses)
                ? item.data_akses
                : [];

            return (
                name.includes(searchLower) ||
                username.includes(searchLower) ||
                aksesArr.some((a) => (a ?? "").toLowerCase().includes(searchLower))
            );
        });

        setDataAkses(filteredData);
    }, [search, aksesList]);

    const columns = [
        // Pilih Baris
        {
            id: "Pilih Baris",
            header: ({ table }) => (
                <Checkbox
                    checked={
                        table.getIsAllPageRowsSelected() ||
                        (table.getIsSomePageRowsSelected() && "indeterminate")
                    }
                    onCheckedChange={(value) =>
                        table.toggleAllPageRowsSelected(!!value)
                    }
                    aria-label="Pilih semua baris"
                />
            ),
            cell: ({ row }) => (
                <Checkbox
                    checked={row.getIsSelected()}
                    onCheckedChange={(value) => row.toggleSelected(!!value)}
                    aria-label="Pilih baris"
                />
            ),
            enableSorting: false,
            enableHiding: false,
        },

        // Identitas
        {
            id: "Identitas",
            accessorKey: "user",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() => {
                        column.toggleSorting(column.getIsSorted() === "asc");
                    }}
                >
                    Identitas
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
            cell: ({ row }) => {
                const user = row?.original?.user ?? null;

                return (
                    <div>
                        <span className="text-gray-400">
                            @{user?.username ?? "-"}
                        </span>
                        <br />
                        <span className="font-medium">
                            {user?.name ?? "(User tidak ditemukan)"}
                        </span>
                    </div>
                );
            },
        },

        // Hak Akses
        {
            id: "Hak Akses",
            accessorKey: "data_akses",
            header: ({ column }) => (
                <Button
                    variant="ghost"
                    onClick={() =>
                        column.toggleSorting(column.getIsSorted() === "asc")
                    }
                >
                    Hak Akses
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
            cell: ({ row }) => {
                const aksesArr = Array.isArray(row?.original?.data_akses)
                    ? row.original.data_akses
                    : [];

                return (
                    <div className="text-left">
                        {aksesArr.length ? (
                            aksesArr.map((akses) => (
                                <Badge
                                    key={akses}
                                    variant="secondary"
                                    className="mr-1 mb-1"
                                >
                                    {akses}
                                </Badge>
                            ))
                        ) : (
                            <span className="text-muted-foreground text-sm">
                                -
                            </span>
                        )}
                    </div>
                );
            },
        },

        // Tindakan
        {
            id: "Tindakan",
            header: "Tindakan",
            cell: ({ row }) => {
                const user = row?.original?.user ?? null;
                const aksesArr = Array.isArray(row?.original?.data_akses)
                    ? row.original.data_akses
                    : [];

                return (
                    <DropdownMenu>
                        <DropdownMenuTrigger asChild>
                            <Button variant="ghost" className="h-8 w-8 p-0">
                                <span className="sr-only">Open menu</span>
                                <Icon.IconDotsVertical />
                            </Button>
                        </DropdownMenuTrigger>

                        <DropdownMenuContent align="end">
                            <DropdownMenuItem
                                className="text-yellow-500"
                                onClick={() => {
                                    setDataEdit({
                                        id: row.original.id,
                                        userId: row.original.user_id,
                                        userName: `@${user?.username ?? "-"} - ${
                                            user?.name ?? "User tidak ditemukan"
                                        }`,
                                        hakAkses: aksesArr,
                                    });
                                    setTitleChangeDialog("Ubah Hak Akses");
                                    setIsChangeDialogOpen(true);
                                }}
                            >
                                <Icon.IconPencil
                                    size={16}
                                    className="mr-2 text-yellow-500"
                                />
                                Ubah
                            </DropdownMenuItem>

                            <DropdownMenuSeparator />

                            <DropdownMenuItem
                                className="text-red-500"
                                onClick={() => {
                                    setDataDelete({
                                        id: row.original.id,
                                        userId: row.original.user_id,
                                        userName: user?.username ?? "-",
                                        hakAkses: aksesArr,
                                    });
                                    setIsDeleteDialogOpen(true);
                                }}
                            >
                                <Icon.IconTrash
                                    size={16}
                                    className="mr-2 text-red-500"
                                />
                                Hapus
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>
                );
            },
        },
    ];

    const table = useReactTable({
        data: dataAkses ?? [],
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
                                <Icon.IconLock className="inline mr-2" />
                                <span>Hak Akses</span>
                            </div>
                        </div>

                        <div className="flex items-center space-x-2">
                            <InputGroup>
                                <InputGroupInput
                                    placeholder="Cari..."
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                />
                                <InputGroupAddon>
                                    <Icon.IconSearch />
                                </InputGroupAddon>
                            </InputGroup>

                            <DropdownMenu>
                                <DropdownMenuTrigger asChild>
                                    <Button variant="outline" className="ml-auto">
                                        Kolom <ChevronDown />
                                    </Button>
                                </DropdownMenuTrigger>
                                <DropdownMenuContent align="end">
                                    <DropdownMenuCheckboxItem
                                        onSelect={(e) => e.preventDefault()}
                                        className="capitalize font-medium"
                                        checked={table
                                            .getAllColumns()
                                            .filter((col) => col.getCanHide())
                                            .every((col) => col.getIsVisible())}
                                        onCheckedChange={(value) => {
                                            table
                                                .getAllColumns()
                                                .filter((col) => col.getCanHide())
                                                .forEach((col) =>
                                                    col.toggleVisibility(!!value)
                                                );
                                        }}
                                    >
                                        Pilih Semua
                                    </DropdownMenuCheckboxItem>

                                    {table
                                        .getAllColumns()
                                        .filter((column) => column.getCanHide())
                                        .map((column) => (
                                            <DropdownMenuCheckboxItem
                                                onSelect={(e) => e.preventDefault()}
                                                key={`column-toggle-${column.id}`}
                                                className="capitalize"
                                                checked={column.getIsVisible()}
                                                onCheckedChange={(value) =>
                                                    column.toggleVisibility(!!value)
                                                }
                                            >
                                                {column.id}
                                            </DropdownMenuCheckboxItem>
                                        ))}
                                </DropdownMenuContent>
                            </DropdownMenu>

                            <Button
                                variant="outline"
                                onClick={() => {
                                    setDataEdit(null);
                                    setTitleChangeDialog("Tambah Hak Akses");
                                    setIsChangeDialogOpen(true);
                                }}
                            >
                                <Icon.IconPlus />
                            </Button>
                        </div>
                    </CardTitle>
                </CardHeader>

                <CardContent>
                    {table.getFilteredSelectedRowModel().rows.length > 0 && (
                        <div className="text-right mb-2">
                            <Button
                                variant="outline"
                                size="sm"
                                className="border-red-500 text-red-500 hover:bg-red-500 hover:text-white"
                                onClick={() => {
                                    const selectedRows =
                                        table.getFilteredSelectedRowModel().rows;

                                    const selectedIds = selectedRows.map(
                                        (r) => r.original.user_id
                                    );

                                    setDataDeleteSelected({
                                        userIds: selectedIds,
                                        userNames: selectedRows.map((r) => {
                                            const u = r.original.user;
                                            return `@${u?.username ?? "-"} - ${
                                                u?.name ?? "User tidak ditemukan"
                                            }`;
                                        }),
                                    });

                                    setIsDeleteSelectedDialogOpen(true);
                                }}
                            >
                                <Icon.IconTrash className="mr-2" />
                                Hapus Semua yang Dipilih (
                                {table.getFilteredSelectedRowModel().rows.length})
                            </Button>
                        </div>
                    )}

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
                                                          header.column.columnDef.header,
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
                                        <TableRow
                                            key={row.id}
                                            data-state={row.getIsSelected() && "selected"}
                                        >
                                            {row.getVisibleCells().map((cell) => (
                                                <TableCell key={cell.id}>
                                                    {flexRender(
                                                        cell.column.columnDef.cell,
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
                                            Belum ada data yang tersedia.
                                        </TableCell>
                                    </TableRow>
                                )}
                            </TableBody>
                        </Table>
                    </div>

                    <div className="flex items-center justify-end space-x-2 py-4">
                        <div className="text-muted-foreground flex-1 text-sm">
                            Memilih {table.getFilteredSelectedRowModel().rows.length} dari{" "}
                            {table.getFilteredRowModel().rows.length} data yang tersedia.
                        </div>
                    </div>
                </CardContent>
            </Card>

            <HakAksesChangeDialog
                dataEdit={dataEdit}
                dialogTitle={titleChangeDialog}
                openDialog={isChangeDialogOpen}
                setOpenDialog={setIsChangeDialogOpen}
            />

            <HakAksesDeleteDialog
                dataDelete={dataDelete}
                openDialog={isDeleteDialogOpen}
                setOpenDialog={setIsDeleteDialogOpen}
            />

            <HakAksesDeleteSelectedDialog
                dataDelete={dataDeleteSelected}
                openDialog={isDeleteSelectedDialogOpen}
                setOpenDialog={setIsDeleteSelectedDialogOpen}
            />
        </AppLayout>
    );
}
