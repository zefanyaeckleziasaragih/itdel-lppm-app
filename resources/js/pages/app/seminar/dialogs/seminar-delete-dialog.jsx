import { Alert, AlertDescription, AlertTitle } from "@/components/ui/alert";
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
import { AlertCircleIcon } from "lucide-react";
import { useEffect } from "react";
import { toast } from "sonner";
import { route } from "ziggy-js";

export function SeminarDeleteDialog({ dataDelete, openDialog, setOpenDialog }) {
    const { data, setData, post, processing } = useForm({
        id: "",
        confirmation: "",
    });

    useEffect(() => {
        if (dataDelete && dataDelete.nama_forum) {
            setData("id", dataDelete.id || "");
        } else {
            setOpenDialog(false);
        }
    }, [dataDelete]);

    const handleSubmit = () => {
        if (data.confirmation !== dataDelete.nama_forum) {
            toast.error("Konfirmasi nama forum tidak sesuai.");
            return;
        }

        post(route("seminar.delete"));
    };

    return (
        <Sheet open={openDialog} onOpenChange={setOpenDialog}>
            <SheetContent aria-describedby="form-dialog">
                <SheetHeader className="pb-0">
                    <SheetTitle>Hapus Seminar</SheetTitle>
                    <SheetDescription>
                        Tindakan ini akan menghapus data seminar yang dipilih.
                    </SheetDescription>
                </SheetHeader>
                <Separator className="my-4" />
                <div className="grid flex-1 auto-rows-min gap-6 px-4">
                    <div className="grid gap-3">
                        <Alert variant="destructive">
                            <AlertCircleIcon />
                            <AlertTitle>Peringatan</AlertTitle>
                            <AlertDescription>
                                <p>
                                    Dengan menghapus seminar ini, semua data
                                    terkait akan hilang dan tidak dapat
                                    dikembalikan.
                                </p>
                            </AlertDescription>
                        </Alert>
                    </div>

                    {dataDelete && dataDelete.nama_forum ? (
                        <div className="grid gap-3">
                            <Label>Nama Forum</Label>
                            <Input
                                value={dataDelete.nama_forum}
                                readOnly={true}
                            />
                        </div>
                    ) : null}

                    <div className="grid gap-3">
                        <Label>Konfirmasi Nama Forum</Label>
                        <Input
                            value={data.confirmation}
                            onChange={(e) =>
                                setData("confirmation", e.target.value)
                            }
                        />
                    </div>
                </div>
                <SheetFooter className="mt-4">
                    <Button
                        onClick={handleSubmit}
                        type="button"
                        className="bg-red-600 hover:bg-red-700"
                        disabled={processing}
                    >
                        {processing ? "Menghapus..." : "Tetap Hapus"}
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
