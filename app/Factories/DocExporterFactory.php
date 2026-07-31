<?php

namespace App\Factories;

use App\DocExporters\ManyTables\DocExporterMonthSchedule;
use App\DocExporters\OneTable\DocExporterTable;
use App\DocExporters\OneTable\WeekSchedule\DocExporterOrdinaryWeekSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterReplacementWeekSchedule;
use App\DocExporters\OneTable\WeekSchedule\DocExporterWeekReschedule;

class DocExporterFactory
{
    public static function createRegWeekScheduleDocExporter(array $data): DocExporterOrdinaryWeekSchedule
    {
        return new DocExporterOrdinaryWeekSchedule($data);
    }

    public static function createMonthScheduleDocExporter(array $data): DocExporterMonthSchedule
    {
        return new DocExporterMonthSchedule($data);
    }

    public static function createWeekRescheduleDocExporter(array $data): DocExporterWeekReschedule
    {
        return new DocExporterWeekReschedule($data);
    }

    public static function createTableDocExporter(array $data): DocExporterTable
    {
        return new DocExporterTable($data);
    }

    public static function createWeekScheduleReplaceDocExporter(array $data): DocExporterReplacementWeekSchedule
    {
        return new DocExporterReplacementWeekSchedule($data);
    }
}