import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
    Sheet,
    SheetClose,
    SheetContent,
    SheetDescription,
    SheetFooter,
    SheetHeader,
    SheetTitle,
} from "@/components/ui/sheet";
import { Separator } from "@/components/ui/separator";
import { useForm } from "@inertiajs/react";
import { useEffect } from "react";
import { route } from "ziggy-js";

export function SeminarFormDialog({
    dataEdit,
    dialogTitle,
    openDialog,
    setOpenDialog,
}) {
    const { data, setData, post, processing, errors, reset } = useForm({
        id: "",
        sinta_id: "",
        scopus_id: "",
        prosiding: "",
        nama_forum: "",
        penulis: "",
        institusi_penyelenggara: "",
        waktu_pelaksanaan: "",
        tempat_pelaksanaan: "",
        url: "",
    });

    useEffect(() => {
        if (dataEdit) {
            setData({
                id: dataEdit.id || "",
                sinta_id: dataEdit.sinta_id || "",
                scopus_id: dataEdit.scopus_id || "",
                prosiding: dataEdit.prosiding || "",
                nama_forum: dataEdit.nama_forum || "",
                penulis: dataEdit.penulis || "",
                institusi_penyelenggara: dataEdit.institusi_penyelenggara || "",
                waktu_pelaksanaan: dataEdit.waktu_pelaksanaan
                    ? new Date(dataEdit.waktu_pelaksanaan)
                          .toISOString()
                          .slice(0, 16)
                    : "",
                tempat_pelaksanaan: dataEdit.tempat_pelaksanaan || "",
                url: dataEdit.url || "",
            });
        } else {
            reset();
        }
    }, [dataEdit]);

    const handleSubmit = () => {
        if (dataEdit) {
            post(route("seminar.update"));
        } else {
            post(route("seminar.store"));
        }
    };

    return (
        <Sheet open={openDialog} onOpenChange={setOpenDialog}>
            <SheetContent
                className="overflow-y-auto"
                aria-describedby="form-dialog"
            >
                <SheetHeader className="pb-0">
                    <SheetTitle>{dialogTitle}</SheetTitle>
                    <SheetDescription>
                        Silahkan isi data seminar pada form di bawah ini.
                    </SheetDescription>
                </SheetHeader>
                <Separator className="my-4" />
                <div className="grid flex-1 auto-rows-min gap-4 px-4">
                    <div className="grid gap-3">
                        <Label htmlFor="sinta_id">Sinta ID</Label>
                        <Input
                            id="sinta_id"
                            value={data.sinta_id}
                            onChange={(e) =>
                                setData("sinta_id", e.target.value)
                            }
                            placeholder="Contoh: 123456"
                        />
                        {errors.sinta_id && (
                            <p className="text-sm text-red-500">
                                {errors.sinta_id}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="scopus_id">Scopus ID</Label>
                        <Input
                            id="scopus_id"
                            value={data.scopus_id}
                            onChange={(e) =>
                                setData("scopus_id", e.target.value)
                            }
                            placeholder="Contoh: 987654"
                        />
                        {errors.scopus_id && (
                            <p className="text-sm text-red-500">
                                {errors.scopus_id}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="prosiding">Prosiding</Label>
                        <Input
                            id="prosiding"
                            value={data.prosiding}
                            onChange={(e) =>
                                setData("prosiding", e.target.value)
                            }
                            placeholder="Pilih salah satu: Penulis 1, Penulis 2"
                        />
                        {errors.prosiding && (
                            <p className="text-sm text-red-500">
                                {errors.prosiding}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="nama_forum">Nama Forum *</Label>
                        <Input
                            id="nama_forum"
                            value={data.nama_forum}
                            onChange={(e) =>
                                setData("nama_forum", e.target.value)
                            }
                            required
                        />
                        {errors.nama_forum && (
                            <p className="text-sm text-red-500">
                                {errors.nama_forum}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="penulis">Penulis</Label>
                        <Input
                            id="penulis"
                            value={data.penulis}
                            onChange={(e) => setData("penulis", e.target.value)}
                            placeholder="Pilih salah satu: Penulis 1, Penulis 2"
                        />
                        {errors.penulis && (
                            <p className="text-sm text-red-500">
                                {errors.penulis}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="institusi_penyelenggara">
                            Institusi Penyelenggara *
                        </Label>
                        <Input
                            id="institusi_penyelenggara"
                            value={data.institusi_penyelenggara}
                            onChange={(e) =>
                                setData(
                                    "institusi_penyelenggara",
                                    e.target.value
                                )
                            }
                            required
                        />
                        {errors.institusi_penyelenggara && (
                            <p className="text-sm text-red-500">
                                {errors.institusi_penyelenggara}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="waktu_pelaksanaan">
                            Waktu Pelaksanaan *
                        </Label>
                        <Input
                            id="waktu_pelaksanaan"
                            type="datetime-local"
                            value={data.waktu_pelaksanaan}
                            onChange={(e) =>
                                setData("waktu_pelaksanaan", e.target.value)
                            }
                            required
                        />
                        {errors.waktu_pelaksanaan && (
                            <p className="text-sm text-red-500">
                                {errors.waktu_pelaksanaan}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="tempat_pelaksanaan">
                            Tempat Pelaksanaan *
                        </Label>
                        <Input
                            id="tempat_pelaksanaan"
                            value={data.tempat_pelaksanaan}
                            onChange={(e) =>
                                setData("tempat_pelaksanaan", e.target.value)
                            }
                            required
                        />
                        {errors.tempat_pelaksanaan && (
                            <p className="text-sm text-red-500">
                                {errors.tempat_pelaksanaan}
                            </p>
                        )}
                    </div>

                    <div className="grid gap-3">
                        <Label htmlFor="url">URL</Label>
                        <Input
                            id="url"
                            type="url"
                            value={data.url}
                            onChange={(e) => setData("url", e.target.value)}
                            placeholder="https://example.com"
                        />
                        {errors.url && (
                            <p className="text-sm text-red-500">{errors.url}</p>
                        )}
                    </div>
                </div>
                <SheetFooter className="mt-4">
                    <Button
                        onClick={handleSubmit}
                        type="button"
                        disabled={processing}
                    >
                        {processing ? "Menyimpan..." : "Simpan"}
                    </Button>
                    <SheetClose asChild>
                        <Button
                            variant="outline"
                            type="button"
                            disabled={processing}
                        >
                            Batal
                        </Button>
                    </SheetClose>
                </SheetFooter>
            </SheetContent>
        </Sheet>
    );
}
