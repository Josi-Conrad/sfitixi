<?php
/**
 * Created by PhpStorm.
 * User: Hert
 * Date: 03.06.14
 * Time: 18:55
 */

namespace Tixi\App\AppBundle\Document;


use Symfony\Component\DependencyInjection\ContainerAware;
use Tixi\App\Document\DocumentManagement;

class DocumentManagementKnp extends ContainerAware implements DocumentManagement {
    /**
     * creates monthplan pdf and returns filepath
     * @param \DateTime $date
     * @return null|string
     */
    public function createMonthPlanDocument(\DateTime $date) {
        $pdfCreator = $this->container->get('knp_snappy.pdf');
        $renderView = $this->container->get('templating');
        $translator = $this->container->get('translator');
        $bankHolidayRep = $this->container->get('bankholiday_repository');
        $workingMonthRepo = $this->container->get('workingmonth_repository');
        $workMonth = $workingMonthRepo->findWorkingMonthByDate($date);
        $timeService = $this->container->get('tixi_api.datetimeservice');

        $dir = $this->container->getParameter('tixi_parameter_files_directory');

        $month = $date->format('F');
        $month = $translator->trans(strtolower($month));
        $year = $date->format('Y');
        $fileName = $dir . '/monthplan/MonthPlan_' . $year . '_' . $month . '.pdf';


        $dayAndShifts = array();
        foreach ($workMonth->getWorkingDays() as $day) {
            $holiday = null;
            try {
                $holiday = $bankHolidayRep->findBankHolidayForDate($day->getDate());
            } catch (\Exception $e) {
            }
            $holidayName = '';
            if ($holiday) {
                $holidayName = ' (' . $holiday->getName() . ')';
            }
            $dayName = $day->getDate()->format('l');
            $dayName = $translator->trans(strtolower($dayName));
            $dayOfMonth = $day->getDate()->format('d');
            $dayTitle = $dayOfMonth . '.' . $month . "\t" . $dayName . $holidayName;

            $shiftAndDrivers = array();
            foreach ($day->getShifts() as $shift) {
                $shiftName = $shift->getShiftType()->getName();
                $shiftStart = $timeService->convertToLocalDateTime($shift->getStart())->format('H:i');
                $shiftEnd = $timeService->convertToLocalDateTime($shift->getEnd())->format('H:i');
                $shiftTitle = $shiftName . "\t" . $shiftStart . '-' . $shiftEnd;

                $driverNames = array();
                foreach ($shift->getDrivingPoolsAsArray() as $drivingPool) {
                    if ($drivingPool->hasAssociatedDriver()) {
                        $driverNames[] = $drivingPool->getDriver()->getNameString();
                    } else {
                        $driverNames[] = '?';
                    }
                }
                $shiftAndDrivers[$shiftTitle] = $driverNames;
            }
            $dayAndShifts[$dayTitle] = $shiftAndDrivers;
        }

        //overwrite existing file
        if (file_exists($fileName)) {
            if (!unlink($fileName)) {
                echo "file locked";
                return null;
            }
        }

        $titleName = $translator->trans('doc.monthplan.title');

        //create pdf from twig template with wkhtmltopdf
        try {
            $pdfCreator->generateFromHtml(
                $renderView->render(
                    'TixiAppBundle:doc:monthPlan.html.twig',
                    array(
                        'title_parameter_name' => $titleName,
                        'title_parameter_month' => $month . ' ' . $year,
                        'dayAndShifts' => $dayAndShifts,
                    )
                ),
                $fileName
            );
        } catch (\Exception $e) {
            return null;
        }
        return $fileName;
    }

    public function sendMonthPlanToAllDrivers(\DateTime $date) {
        $file = $this->createMonthPlanDocument($date);
        if ($file === null) {
            return false;
        }
        $driverRepo = $this->container->get('driver_repository');
        $drivers = $driverRepo->findAllActive();
        $mailService = $this->container->get('tixi_app.mailservice');

        $mailAddresses = array();
        foreach ($drivers as $driver) {
            $mailTo = $driver->getEmail();
            if (!empty($mailTo)) {
                $mailAddresses[] = $mailTo;
            }
        }
        if (count($mailAddresses) < 1) {
            echo "no available mail addresses to send";
            return false;
        }

        $renderView = $this->container->get('templating');
        $html = $renderView->render('@TixiApp/mail/monthPlanMail.html.twig');

        $subject = 'Monatsplan';
        $sendSuccess = $mailService->mailToSeveralRecipients($mailAddresses, $subject, $html, $file);
        if (!$sendSuccess) {
            return false;
        }
        return true;
    }
}