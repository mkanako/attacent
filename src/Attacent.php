<?php

namespace Cc\Attacent;

use Cc\Attacent\Models\Attachment;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Attacent
{
    private $uid = 0;
    private $pageSize = 20;
    private $disk;
    private $prefix = '';

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

    public function setPageSize($pageSize)
    {
        $this->pageSize = $pageSize;
        return $this;
    }

    public function setPrefix($prefix)
    {
        $this->prefix = $prefix . '/';
        return $this;
    }

    public function upload(UploadedFile $file)
    {
        $allowed_ext = config('attacent.allowed_ext', []);
        $type = strstr($file->getMimeType(), '/', true);
        if ($type && array_key_exists($type, $allowed_ext) && (1 === preg_match('/^(' . $allowed_ext[$type] . ')$/i', $file->extension()))) {
            $path = $this->disk->putFile($this->prefix . $type . '/' . date('Y/m/d'), $file);
            if (empty($path)) {
                throw new \Exception('write file error');
            }
            $attach = new Attachment();
            $attach->path = $path;
            $attach->type = $type;
            $attach->filename = $file->getClientOriginalName();
            $attach->uid = $this->uid;
            $attach->save();
            return [
                'url' => $attach->url,
                'filename' => $attach->filename,
                'path' => $attach->path,
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
            'pageSize' => $this->pageSize,
            'page' => $page,
            'data' => $attach
                ->orderBy('id', 'desc')
                ->offset(($page - 1) * $this->pageSize)
                ->limit($this->pageSize)
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
