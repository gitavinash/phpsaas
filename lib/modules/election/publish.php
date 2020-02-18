<?php

    /** Add New Constituency Function */

    function constituency($connection, $data) {

        extract($data);

        /** Check constituency is alreay exists or not */

        if($checkConstituency=mysqli_query($connection, "SELECT constituency_id FROM ".TABLE_PREFIX."constituency WHERE constituency_name='{$name}' AND constituency_state='{$state}' AND constituency_type='{$category}'")) {

            if(mysqli_num_rows($checkConstituency)==0) {

                /** Create new constituency */

                if(mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."constituency(constituency_name, constituency_state, constituency_type, constituency_year) VALUES('{$name}', '{$state}', '{$category}', NOW())")) {

                    $id=mysqli_insert_id($connection);
                    $status=200;
                    $msg='New constituency has been created successfully';

                }

            } else {

                $msg="This constituency has been already exits";

            }

        }

		$result=array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong when creating a new constituency' ),
			'id' => ((isset($insertID)) ? $insertID : 0 )
		);

		echo json_encode($result, TRUE);

    }

    /** Add New Party Function */
 
    function party($connection, $data) {

        extract($data);

        /** Check party is alreay exists or not */

        if($checkParty=mysqli_query($connection, "SELECT party_id FROM ".TABLE_PREFIX."party WHERE party_name='{$name}'")) {

            if(mysqli_num_rows($checkParty)==0) {

                /** Create new Party */

                if(mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."party(party_code, party_name, party_slogan) VALUES('{$code}', '{$name}', '{$slogan}')")) {

                    $id=mysqli_insert_id($connection);
                    $status=200;
                    $msg='New party has been created successfully';

                }

            } else {

                $msg="This party has been already exits";

            }

        }

		$result=array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong when creating a new party' ),
			'id' => ((isset($insertID)) ? $insertID : 0 )
		);

		echo json_encode($result, TRUE);

    }

    /** Add New Election Function */

    function election($connection, $data) {

        extract($data);

        /** Check election is alreay exists or not */

        if($checkElection=mysqli_query($connection, "SELECT election_id FROM ".TABLE_PREFIX."election WHERE election_state='{$state}' AND election_type='{$category}' AND election_year='{$year}'")) {

            if(mysqli_num_rows($checkElection)==0) {

                /** Create new election */

                mysqli_query($connection, "START TRANSACTION");

                $creatElection=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."election(election_name, election_state, election_party_1, election_party_2, election_party_3, election_party_4, election_total_seats,  election_type, election_date, election_result_date, election_year) VALUES('{$name}', '{$state}', '{$party_1}', '{$party_2}', '{$party_3}', '{$party_4}', '{$seat}', '{$category}', NOW(), NOW(), '{$year}')");
                $electionID=mysqli_insert_id($connection);

                $creatResult=mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."result(result_election_id, result_status) VALUES('{$electionID}', 0)");

                if($creatElection && $creatResult) {

                    mysqli_query($connection, "COMMIT");

                    $id=mysqli_insert_id($connection);
                    $status=200;
                    $msg='New election has been created successfully';

                } else {

                    mysqli_query($connection, "ROLLBACK");

                }

            } else {

                $msg="This election has been already exits";

            }

        }

		$result=array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong when creating a new election' ),
			'id' => ((isset($insertID)) ? $insertID : 0 )
		);

		echo json_encode($result, TRUE);

    }

    /** Add New Candidate Function */

    function candidate($connection, $data) {

        extract($data);

        /** Check candidate is alreay exists or not */

        if($checkCandidate=mysqli_query($connection, "SELECT candidate_id FROM ".TABLE_PREFIX."candidate WHERE candidate_party='{$party}' AND candidate_constituency='{$constituency}' AND candidate_election='{$election}' AND candidate_name='{$name}'")) {

            if(mysqli_num_rows($checkCandidate)==0) {

                /** Create new candidate */

                if(mysqli_query($connection, "INSERT INTO ".TABLE_PREFIX."candidate(candidate_name, candidate_party, candidate_election, candidate_constituency) VALUES('{$name}', '{$party}', '{$election}', '{$constituency}')")) {

                    $id=mysqli_insert_id($connection);
                    $status=200;
                    $msg='New candidate has been created successfully';

                }

            } else {

                $msg="This candidate has been already exits";

            }

        }

		$result=array(
			'status' => ((isset($status)) ? $status : 500 ),
			'msg' => ((isset($msg)) ? $msg : 'Something is wrong when creating a new candidate' ),
			'id' => ((isset($insertID)) ? $insertID : 0 )
		);

		echo json_encode($result, TRUE);

    }