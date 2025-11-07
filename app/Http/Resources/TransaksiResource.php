<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransaksiResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kode_transaksi' => $this->kode_transaksi,
            'nama_transaksi' => $this->nama_transaksi,
            'jumlah' => $this->jumlah,
            'tanggal' => $this->tanggal->format('Y-m-d'),
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'business_unit' => new BusinessUnitResource($this->whenLoaded('businessUnit')),
            'user' => new UserResource($this->whenLoaded('user')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
