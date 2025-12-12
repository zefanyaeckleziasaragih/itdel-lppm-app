import { useState, useEffect } from "react";
import { usePage, Link } from "@inertiajs/react";
import { route } from "ziggy-js";
import AppLayout from "@/layouts/app-layout";
import { Card, CardContent } from "@/components/ui/card";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from "@/components/ui/select";
import { AUTH_TOKEN_KEY } from "@/lib/consts";
import { Award } from "lucide-react";

export default function DaftarPenghargaanPage() {
    const { authToken, penghargaanList = [] } = usePage().props;
    const [searchQuery, setSearchQuery] = useState("");
    const [searchBy, setSearchBy] = useState("all");
    const [sortBy, setSortBy] = useState("newest");

    useEffect(() => {
        if (authToken) {
            localStorage.setItem(AUTH_TOKEN_KEY, authToken);
        } else {
            window.location.href = route("auth.logout");
        }
    }, []);

    return (
        <AppLayout>
            <div className="space-y-6">
                {/* Header */}
                <h1 className="text-2xl font-semibold">
                    Tampilan Laman Pencarian Dana
                </h1>

                {/* Search Bar */}
                <div className="flex gap-4">
                    <div className="flex-1 relative">
                        <Input
                            type="text"
                            placeholder="Type to search"
                            value={searchQuery}
                            onChange={(e) => setSearchQuery(e.target.value)}
                            className="pr-24"
                        />
                        <Button
                            className="absolute right-1 top-1/2 -translate-y-1/2 h-8"
                            size="sm"
                        >
                            Search
                        </Button>
                    </div>

                    <Select value={searchBy} onValueChange={setSearchBy}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Search by" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="all">Search by</SelectItem>
                            <SelectItem value="title">Title</SelectItem>
                            <SelectItem value="author">Author</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select value={sortBy} onValueChange={setSortBy}>
                        <SelectTrigger className="w-[180px]">
                            <SelectValue placeholder="Sort by" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="newest">Sort by</SelectItem>
                            <SelectItem value="oldest">Oldest</SelectItem>
                            <SelectItem value="title">Title</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                {/* List Items */}
                <div>
                    {penghargaanList && penghargaanList.length > 0 ? (
                        penghargaanList.map((item) => (
                            <Link
                                key={item.id}
                                href={route(
                                    "daftar-penghargaan.detail",
                                    item.id
                                )}
                                className="block mb-4" // <= Tambahkan jarak antar card
                            >
                                <Card className="hover:shadow-md transition-shadow cursor-pointer border">
                                    <CardContent className="p-4">
                                        <div className="flex items-center justify-between">
                                            <div className="flex items-center gap-4">
                                                <div className="w-10 h-10 bg-black dark:bg-white rounded-full flex items-center justify-center flex-shrink-0">
                                                    <Award className="w-5 h-5 text-white dark:text-black" />
                                                </div>
                                                <div>
                                                    <div className="font-semibold text-foreground">
                                                        {item.title}
                                                    </div>
                                                    <div className="text-sm text-muted-foreground">
                                                        {Array.isArray(
                                                            item.penulis
                                                        )
                                                            ? item.penulis.join(
                                                                  ", "
                                                              )
                                                            : item.penulis}
                                                    </div>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <div className="text-sm text-muted-foreground mb-1">
                                                    Status :{" "}
                                                    <span
                                                        className={
                                                            item.status ===
                                                            "Disetujui"
                                                                ? "text-green-600 dark:text-green-500"
                                                                : ""
                                                        }
                                                    >
                                                        {item.status}
                                                    </span>
                                                </div>
                                                <div className="text-sm text-muted-foreground">
                                                    {item.date}
                                                </div>
                                            </div>
                                        </div>
                                    </CardContent>
                                </Card>
                            </Link>
                        ))
                    ) : (
                        <Card>
                            <CardContent className="p-8 text-center text-muted-foreground">
                                Tidak ada data penghargaan
                            </CardContent>
                        </Card>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
