import { Button } from "@/components/ui/button";
import { Label } from "@/components/ui/label";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
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

export function SeminarStatusDialog({ dataStatus, openDialog, setOpenDialog }) {
    const { data, setData, post, processing } = useForm({
        id: "",
        status: "",
    });

    useEffect(() => {
        if (dataStatus) {
            setData({
                id: dataStatus.id || "",
                status: dataStatus.status || "Belum Dicairkan",
            });
        }
    }, [dataStatus]);

    const handleSubmit = () => {
        post(route("seminar.update-status"));
    };

    return (
        <Sheet open={openDialog} onOpenChange={setOpenDialog}>
            <SheetContent aria-describedby="form-dialog">
                <SheetHeader className="pb-0">
                    <SheetTitle>Ubah Status Seminar</SheetTitle>
                    <SheetDescription>
                        Silahkan pilih status pencairan dana seminar.
                    </SheetDescription>
                </SheetHeader>
                <Separator className="my-4" />
                <div className="grid flex-1 auto-rows-min gap-6 px-4">
                    <div className="grid gap-3">
                        <Label htmlFor="status">Status</Label>
                        <Select
                            value={data.status}
                            onValueChange={(value) => setData("status", value)}
                        >
                            <SelectTrigger id="status">
                                <SelectValue placeholder="Pilih Status" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="Belum Dicairkan">
                                    Belum Dicairkan
                                </SelectItem>
                                <SelectItem value="Sudah Dicairkan">
                                    Sudah Dicairkan
                                </SelectItem>
                            </SelectContent>
                        </Select>
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
