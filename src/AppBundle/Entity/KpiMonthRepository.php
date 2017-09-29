<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

use Application\Sonata\UserBundle\Entity\User;

/**
 * KpiMonthRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class KpiMonthRepository extends EntityRepository
{
	public function getUserKpisBetweenDates(User $user, $date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.user = :user')
		  	->setParameter('user', $user)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->orderBy('k.date', 'DESC')
		;

		return $qb
			->getQuery()
			->getResult();
	}

	public function getTop3Ca($brand, $date){
		$qb = $this
			->createQueryBuilder('k')
		  	->orderBy('k.caClientsTransformesM0', 'DESC')
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->where('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('u.role = :role')
		  	->andWhere('k.date = :date')
		  	->andWhere('u.ispremium != 1')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->setParameter('date', $date)
		  	->setMaxResults(3);
		;

		return $qb
			->getQuery()
			->getResult();
	}

	public function getRank1Npe($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpeM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpeM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpeVendeur($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpeM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpeM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1Npes($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesM0= :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpesM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpesVendeur($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesM0= :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpesM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1Npesa($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpesaM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpesaVendeur($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpesaM0', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpeYtd($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpeYtd = :val')
			->setParameter(':val', 1)
			->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpeYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpesYtd($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesYtd= :val')
			->setParameter(':val', 1)
			->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpesYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpesaYtd($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaYtd = :val')
			->setParameter(':val', 1)
			->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')		  	
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.rankNpesaYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1_3NPS($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->where('u.brand = :brand')		  	
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->orderBy('k.questsatisfranknpsm0', 'ASC')
		  	->setMaxResults(3);
		;

		return $qb
			->getQuery()
			->getResult();
	}



	public function getRank1NpeYtdVendeur($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpeYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}


	public function getRank1NpesYtdVendeur($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpesYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}

	public function getRank1NpesaYtdVendeur($date1,$date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
			->where('k.rankNpesaM0 = :val')
			->setParameter(':val', 1)
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->orderBy('k.txTransacNpesaYtd', 'DESC')
		  	->setMaxResults(1);
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
	}


	public function getKpiVendeurBoutique($boutique, $date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
			->where('u.boutique = :boutique')
		  	->setParameter('boutique', $boutique)
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_VENDEUR")
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->orderBy('u.username', 'ASC')
		;

		return $qb
			->getQuery()
			->getResult();
	}


	public function getKpiBoutiqueDr($dr, $date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
			->where('u.dr = :dr')
		  	->setParameter('dr', $dr)
		  	->andWhere('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_BOUTIQUE")
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->orderBy('u.username', 'ASC')
		;

		return $qb
			->getQuery()
			->getResult();
	}


	public function getKpiDrMarque($date1, $date2, $brand){
		$qb = $this
			->createQueryBuilder('k')
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
			->where('u.brand = :brand')
		  	->setParameter('brand', $brand)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_DR")
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->orderBy('u.username', 'ASC')
		;

		return $qb
			->getQuery()
			->getResult();
	}


	public function getKpiMarque($date1, $date2, $username){
		$qb = $this
			->createQueryBuilder('k')
		  	->leftJoin('k.user', 'u')
		  	->addSelect('u')
			->where('u.username = :username')
		  	->setParameter('username', $username)
		  	->andWhere('u.role = :role')
		  	->setParameter('role', "ROLE_MARQUE")
		  	->andWhere('k.date BETWEEN :date1 AND :date2')
		  	->setParameter('date1', $date1)
		  	->setParameter('date2', $date2)
		  	->orderBy('u.username', 'ASC')
		;

		return $qb
			->getQuery()
			->getOneOrNullResult();
			//->getResult();
	}
}
