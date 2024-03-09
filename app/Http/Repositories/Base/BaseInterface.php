<?php

namespace App\Http\Repositories\Base;

interface BaseInterface
{
  public function findBySlug($slug);
  public function findBySlugPulk($slugs);
  public function findById($id);
  public function findByIdPulk($ids);
  public function findTrashedById($id);
  public function delete($id);
  public function deletePulk($ids);
//   public function restore($request, $id);
  public function forceDelete($id);
  public function setLocales($model, $locales, $slug=false, $generatedSlug=null);
  public function setSocials($model, $socials);
  public function setSortParams($request);
  public function getWith($models, $with);
  public function getWithCount($models, $withCount);
  public function generateSlug($model, $name, $column='slug');
  public function setImages($model, $images);
  public function editPulk($ids, $status);
}