<?php

namespace AmeshExtensions\CustomerSupport\Model\Zoom\ServiceProvider;

class MeetingApiConfigManager
{
    private $configurations;

    public function create($data)
    {
        foreach ($data as $key => $settings) {
            $this->configurations[$key] = $settings;
        }        
    }

    public function getConfigns()
    {
        $allConfigurations = $this->configurations;
        unset($allConfigurations['agent_id']);
        unset($allConfigurations['agent_email']);
        unset($allConfigurations['form_key']);

        return $allConfigurations;
    }

    public function getTopic()
    {
        return $this->configurations['topic'];
    }

    public function setTopic($topic)
    {
        $this->configurations['topic'] = $topic;
    }

    public function getType()
    {
        return $this->configurations['type'];
    }

    public function setType($type)
    {
        $this->configurations['type'] = $type;
    }

    public function getAgentId()
    {
        return $this->configurations['agent_id'];
    }

    public function setAgentId($agentId)
    {
        $this->configurations['agent_id'] = $agentId;
    }

    public function getAgentEmail()
    {
        return $this->configurations['agent_email'];
    }

    public function setAgentEmail($agentEmail)
    {
        $this->configurations['agent_email'] = $agentEmail;
    }

    public function getStartTime()
    {
        return $this->configurations['start_time'];
    }

    public function setStartTime($startTime)
    {
        $this->configurations['start_time'] = $startTime;
    }

    public function getDuration()
    {
        return $this->configurations['duration'];
    }

    public function setDuration($duration)
    {
        $this->configurations['duration'] = $duration;
    }

    public function getPassword()
    {
        return $this->configurations['password'];
    }

    public function setPassword($password)
    {
        $this->configurations['password'] = $password;
    }

    public function getSettings()
    {
        return $this->configurations['settings'];
    }

    public function setSettings(array $settings)
    {
        $this->configurations['settings'] = $settings;
    }

    public function clear()
    {
        $this->configurations = [];
    }
}
