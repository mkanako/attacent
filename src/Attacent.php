<?php

namespace Cc\Attacent;

use Cc\Attacent\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Attacent
{
    private $uid = 0;
    private $perPage = 20;
    private $disk;

    public function __construct()
    {
        $this->disk = Storage::disk('attacent');
        app()->instance('Cc\Attacent\disk', $this->disk);
    }

    public function setUid($uid)
    {
        $this->uid = $uid;
        return $this;
    }

    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
        return $this;
    }

    public function upload(UploadedFile $file)
    {
        $fileTypes = config('attacent.fileTypes', []);
        $type = strstr($file->getMimeType(), '/', true);
        if ($type && array_key_exists($type, $fileTypes) && (1 === preg_match($fileTypes[$type], $file->extension()))) {
            $date = date('Y/m/d');
            $this->disk->putFile($type . '/' . $date, $file);
            $attach = new Attachment();
            $attach->path = $date . '/' . $file->hashName();
            $attach->type = $type;
            $attach->filename = $file->getClientOriginalName();
            $attach->uid = $this->uid;
            $attach->save();
            return [
                'url' => $attach->url,
                'filename' => $attach->filename,
                'id' => $attach->id,
            ];
        }

        throw new \Exception('invalid file');
    }

    public function getList($page = 1, $type = 'image', $filter = ['year' => null, 'month' => null])
    {
        $page = max(intval($page), 1);
        $attach = Attachment::where('uid', $this->uid)
            ->where('type', $type)
            ->where(function ($query) use ($filter) {
                if (!empty($filter['year'])) {
                    $query->whereYear('created_at', $filter['year']);
                    if (!empty($filter['month'])) {
                        $query->whereMonth('created_at', $filter['month']);
                    }
                }
            });
        return [
            'total' => $attach->count(),
            'perPage' => $this->perPage,
            'page' => $page,
            'data' => $attach
                ->orderBy('id', 'desc')
                ->offset(($page - 1) * $this->perPage)
                ->limit($this->perPage)
                ->get(),
        ];
    }

    public function delete($id)
    {
        $attach = Attachment::find($id);
        if ($attach) {
            $attach->delete();
            if ($attach->path && $this->disk->exists($attach->path)) {
                $this->disk->delete($attach->path);
                return true;
            }

            throw new \Exception($attach->path . ' not exist');
        }

        throw new \Exception('db record not exist');
    }
}
